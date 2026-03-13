// معالجة نموذج تسجيل الدخول
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const errorMsg = document.getElementById('error');
    
    // مسح الرسالة السابقة
    errorMsg.textContent = '';
    
    // التحقق من عدم ترك الحقول فارغة
    if (!username || !password) {
        errorMsg.textContent = 'Please fill in all fields';
        return;
    }
    
    // تحقق بسيط من البيانات (يمكن تغييرها)
    // في التطبيق الفعلي، يجب إرسال البيانات إلى الخادم
    if (username === 'admin' && password === 'password123') {
        // تسجيل دخول ناجح
        localStorage.setItem('user', username);
        window.location.href = 'home.html'; // إعادة التوجيه للصفحة الرئيسية
    } else {
        // بيانات خاطئة
        errorMsg.textContent = 'Invalid username or password';
        document.getElementById('password').value = ''; // مسح الرمز السري
    }
});

// تفريغ الرسالة عند التركيز على حقل الإدخال
document.getElementById('username').addEventListener('focus', function() {
    document.getElementById('error').textContent = '';
});

document.getElementById('password').addEventListener('focus', function() {
    document.getElementById('error').textContent = '';
});
