@php
    use App\Models\User;
    use Behin\SimpleWorkflow\Models\Entities\Users_profile;
    use Behin\SimpleWorkflow\Models\Entities\Requests;
    use Behin\SimpleWorkflow\Models\Entities\Panels;
    use Behin\SimpleWorkflow\Models\Entities\Invertors;
    use Behin\SimpleWorkflow\Models\Entities\Request_panels;
    use Behin\SimpleWorkflow\Models\Entities\Request_invertors;
    use Behin\SimpleWorkflow\Models\Entities\Powerhouse_place_info;
    use Behin\SimpleWorkflow\Models\Entities\Request_batteries;

    $customer = Users_profile::find($case->getVariable('user_profile_id'));
    $powerhousePlaceInfo = Powerhouse_place_info::find($case->getVariable('powerhouse_place_info-id'));
    $contractor = User::find($case->getVariable('request-contractor_id'));
    $technicianHead = User::find($case->getVariable('request-technician_head_id'));
    $inspector = User::find($case->getVariable('inspector'));
    $request = Requests::find($case->getVariable('request-id'));
    $usedPanels = Request_panels::where('request_id', $request->id)->get();
    $usedInvertors = Request_invertors::where('request_id', $request->id)->get();
    $usedBatteries = Request_batteries::where('request_id', $request->id)->get();
@endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<style>
    @media print {
        @page {
            size: A4;
        }

        body * {
            visibility: hidden;
        }

        #printable-area,
        #printable-area * {
            visibility: visible;
        }

        #printable-area {
            background: white;
            padding: 30px;
            width: 210mm;
            min-height: 297mm;
            margin: auto;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        .no-print {
            display: none !important;
        }
    }
</style>
<div id="printable-area" style="background: white !important;">



    <style>
        body {
            direction: rtl;
            /* font-family: "Tahoma", sans-serif; */
            /* background-color: #f9f9f9; */
            /* color: #333; */
            /* padding: 20px; */
        }

        #printable-area {
            background: white;
            padding: 30px;
            width: 210mm;
            min-height: 297mm;
            margin: auto;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        .section-title {
            background-color: #0d6efd;
            color: white;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            font-weight: bold;
        }

        table th,
        table td {
            vertical-align: middle !important;
            text-align: center;
        }

        #powerhousePlaceInfo td {
            text-align: right;
        }

        #customerInfo td {
            text-align: right;
        }

        .logo-container img {
            max-height: 80px;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="logo-container"><img src="{{ url('public/behin/logo.png') }}" alt="Logo"></div>
        <div class="text-center">
            <h5>گواهی سلامت نیروگاه خورشیدی</h5>
            <h6>شناسه درخواست: {{ $request->id }}</h6>
            <h6>تاریخ صدور: <span dir="ltr">{{ toJalali(now())->format('Y-m-d') }}</span></h6>
        </div>
        <div class="logo-container"><img src="{{ url('public/behin/samt.jpg') }}" alt="Logo"></div>
    </div>

    {{-- اطلاعات مشتری --}}
    <div class="mb-4">
        <div class="section-title">اطلاعات مشتری</div>
        <div class="table-responsive">
            <table class="table table-sm" id="customerInfo">
                @if ($customer->type == 'حقیقی')
                    <tr>
                        <td><strong>نوع:</strong> {{ $customer->type }}</td>
                        <td><strong>کد ملی:</strong> {{ $customer->national_id }}</td>
                    </tr>
                    <tr>
                        <td><strong>نام:</strong> {{ $customer->firstname }}</td>
                        <td><strong>نام خانوادگی:</strong> {{ $customer->lastname }}</td>
                    </tr>
                @else
                    <tr>
                        <td><strong>نوع:</strong> {{ $customer->type }}</td>
                        <td><strong>نام حقوقی:</strong> {{ $customer->legal_name }}</td>
                        <td><strong>شناسه ملی:</strong> {{ $customer->legal_national_id }}</td>
                    </tr>
                    <tr>
                        <td><strong>شماره ثبت:</strong> {{ $customer->legal_register_number }}</td>
                        <td><strong>تاریخ ثبت:</strong> {{ $customer->legal_register_date }}</td>
                    </tr>
                @endif
            </table>
        </div>
    </div>

    {{-- محل نیروگاه --}}
    @if ($powerhousePlaceInfo)
        <div class="mb-4">
            <div class="section-title">اطلاعات محل نیروگاه</div>
            <div class="table-responsive">
                <table class="table table-sm" id="powerhousePlaceInfo">
                    <tr>
                        <td><strong>نام:</strong>{{ $powerhousePlaceInfo->name }}</td>
                        <td><strong>استان:</strong>{{ $powerhousePlaceInfo->province }}</td>
                        <td><strong>شهر:</strong>{{ $powerhousePlaceInfo->city }}</td>
                        <td><strong>کد پستی:</strong>{{ $powerhousePlaceInfo->postal_code }}</td>
                    </tr>
                    <tr>
                        <td colspan="4"><strong>آدرس:</strong>{{ $powerhousePlaceInfo->address }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>طول جغرافیایی:</strong> {{ $powerhousePlaceInfo->lng1 }}</td>
                        <td colspan="2"><strong>عرض جغرافیایی:</strong> {{ $powerhousePlaceInfo->lat1 }}</td>
                    </tr>
                </table>

            </div>
        </div>
    @endif

    {{-- پنل‌ها --}}
    <div class="mb-4">
        <div class="section-title">پنل‌های استفاده‌شده</div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-primary">
                    <tr>
                        <th>سریال</th>
                        <th>مدل</th>
                        <th>سازنده</th>
                        <th>حداکثر توان</th>
                        <th>بازدهی</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usedPanels as $item)
                        @php $panel = $item->panel(); @endphp
                        @if ($panel)
                            <tr>
                                <td>{{ $panel->serial_number }}</td>
                                <td>{{ $panel->model_number }}</td>
                                <td>{{ $panel->manufacturer }}</td>
                                <td>{{ $panel->max_power_output }}</td>
                                <td>{{ $panel->efficiency }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- اینورترها --}}
    <div class="mb-4">
        <div class="section-title">اینورترهای استفاده‌شده</div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-primary">
                    <tr>
                        <th>سریال</th>
                        <th>سازنده</th>
                        <th>تاریخ تولید</th>
                        <th>کشور سازنده</th>
                        <th>حداکثر توان خروجی</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usedInvertors as $item)
                        @php $inv = $item->invertor(); @endphp
                        @if ($inv)
                            <tr>
                                <td>{{ $inv->serial }}</td>
                                <td>{{ $inv->manufacturer }}</td>
                                <td>{{ $inv->production_date }}</td>
                                <td>{{ $inv->country_of_origin }}</td>
                                <td>{{ $inv->max_power_output }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- باطری ها --}}
    <div class="mb-4">
        <div class="section-title">باطری های استفاده‌شده</div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-primary">
                    <tr>
                        <th>سریال</th>
                        <th>سازنده</th>
                        <th>تاریخ تولید</th>
                        <th>کشور سازنده</th>
                        <th>ولتاژ</th>
                        <th>ظرفیت</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usedBatteries as $item)
                        @php $bat = $item->battery(); @endphp
                        @if ($bat)
                            <tr>
                                <td>{{ $bat->serial }}</td>
                                <td>{{ $bat->manufacturer }}</td>
                                <td>{{ $bat->production_date }}</td>
                                <td>{{ $bat->country_of_origin }}</td>
                                <td>{{ $bat->nominal_voltage }}</td>
                                <td>{{ $bat->nominal_capacity }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- پرسنل --}}
    <div class="mb-4">
        <div class="section-title">اطلاعات صادر کننده</div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" style="width: 100%;">
                <tr>
                    <td>
                        @if ($contractor)
                            <div class="col-md-4"><strong>پیمانکار:</strong> {{ $contractor->name }}</div>
                        @endif
                    </td>
                    <td>
                        @if ($technicianHead)
                            <div class="col-md-4"><strong>تکنسین ارشد:</strong> {{ $technicianHead->name }}</div>
                        @endif
                    </td>
                    <td>
                        @if ($inspector)
                            <div class="col-md-4"><strong>بازرس:</strong> {{ $inspector->name }}</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

{{-- دکمه‌ها --}}
<div class="text-center mt-4 no-print">
    <button onclick="printDiv()" class="btn btn-outline-primary">چاپ</button>
    {{-- <button onclick="downloadPDF()" class="btn btn-outline-danger">دانلود PDF A4</button> --}}
</div>


{{-- اسکریپت ذخیره تصویر --}}
<script>
    function printDiv() {
        const content = document.getElementById('printable-area').innerHTML;
        const original = document.body.innerHTML;
        document.body.innerHTML = content;
        window.print();
        document.body.innerHTML = original;
        location.reload();
    }

    function downloadPDF() {
        const element = document.getElementById('printable-area');
        const opt = {
            margin: 0.5,
            filename: 'certificate.pdf',
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 1
            },
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'portrait'
            }
        };
        html2pdf().from(element).set(opt).save();
    }
</script>
