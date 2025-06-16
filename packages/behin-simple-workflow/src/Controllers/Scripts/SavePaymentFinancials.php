<?php
namespace Behin\SimpleWorkflow\Controllers\Scripts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Behin\SimpleWorkflow\Models\Entities\Financials;
use Morilog\Jalali\Jalalian;

class SavePaymentFinancials extends Controller
{
    public function execute(Request $request = null)
    {
        $paymentAmounts = $request->input('payment_amount', []);
        $paymentDates = $request->input('payment_date', []);
        $paymentDescriptions = $request->input('payment_description', []);

        foreach ($paymentAmounts as $id => $amount) {
            $financial = Financials::find($id);

            if (!$financial) {
                continue; // اگر ردیفی پیدا نشد، رد شو
            }

            $dateRaw = $paymentDates[$id] ?? null;
            $paymentDate = null;

            // تبدیل تاریخ شمسی به یونیکس تایم‌استمپ
            if ($dateRaw) {
                try {
                    $dateRaw = convertPersianToEnglish($dateRaw);
                    $paymentDate = Jalalian::fromFormat('Y-m-d', $dateRaw)->toCarbon()->timestamp;
                } catch (\Exception $e) {
                    $paymentDate = null; // اگر خطا داشت، هیچی
                }
            }

            $financial->payment = str_replace(',', '', $amount);
            $financial->payment_date = $paymentDate;
            $financial->payment_description = $paymentDescriptions[$id] ?? null;

            $financial->save();
        }
    }
}