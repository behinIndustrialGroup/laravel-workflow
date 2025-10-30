    
    <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-md text-center">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">نوع کاربری خود را انتخاب کنید</h2>
      
      <select name="use_type" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-yellow-400 focus:outline-none text-gray-700">
        <option value="">-- انتخاب کنید --</option>
        <option value="residential">مسکونی</option>
        <option value="commercial">تجاری</option>
        <option value="office">اداری</option>
        <option value="guild">صنفی</option>
        <option value="industrial">صنعتی</option>
        <option value="agriculture">کشاورزی</option>
      </select>

      <button onclick="saveAndNextForm()" class="mt-6 w-full bg-yellow-400 hover:bg-yellow-500 text-white font-semibold py-3 rounded-xl transition">
        ادامه
      </button>
    </div>