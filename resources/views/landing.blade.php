<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-Library | Perpustakaan Digital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f0f4f8;
            color: #1a2332;
        }
        .art-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-style: italic;
        }

        /* ===== ANIMASI ===== */
        @keyframes logoPop {
            0% { transform: scale(0) rotate(-180deg); opacity: 0; }
            60% { transform: scale(1.15) rotate(5deg); opacity: 1; }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        .logo-anim {
            animation: logoPop 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }
        .anim-fade-up {
            animation: fadeInUp 0.7s ease forwards;
            opacity: 0;
        }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }

        /* ===== NAVBAR ===== */
        .navbar {
            background: #ffffff;
            border-bottom: 2px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .logo-img {
            height: 48px;
            width: auto;
            object-fit: contain;
            display: block;
        }

        .btn-masuk {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 10px 28px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .btn-masuk:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
        }

        /* ===== HERO ===== */
        .hero {
            min-height: 70vh;
            display: flex;
            align-items: center;
            position: relative;
            background: #0b1a33;
            color: #fff;
        }
        .hero-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.3;
        }
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: rgba(11, 26, 51, 0.75);
        }
        .hero-content {
            position: relative;
            z-index: 2;
            width: 100%;
            padding: 40px 0;
        }

        /* ===== FEATURE ===== */
        .feature-section {
            padding: 70px 0;
            background: #f0f4f8;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-style: italic;
            font-size: 2.4rem;
            color: #0b1a33;
        }
        .section-title span { color: #2563eb; }
        .section-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #2563eb;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-top: 32px;
        }
        .feature-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 32px 28px;
            border: 2px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            cursor: default;
        }
        .feature-card:hover {
            transform: translateY(-8px) scale(1.01);
            box-shadow: 0 16px 40px rgba(37, 99, 235, 0.12);
            border-color: #2563eb;
        }
        .feature-card-large {
            grid-row: span 2;
        }
        
        .feature-number {
            font-size: 2rem;
            font-weight: 800;
            color: #2563eb;
            letter-spacing: -0.02em;
            margin-bottom: 8px;
        }
        .feature-title {
            font-weight: 800;
            font-size: 1.15rem;
            margin: 0 0 6px 0;
            color: #0b1a33;
        }
        .feature-desc {
            color: #475569;
            font-size: 0.92rem;
            line-height: 1.6;
        }
        .feature-tags {
            display: flex;
            gap: 18px;
            margin-top: 18px;
            border-top: 1px solid #e8effa;
            padding-top: 14px;
            font-size: 0.78rem;
            font-weight: 600;
            color: #2563eb;
        }
        .feature-tags i { margin-right: 6px; }

        /* ===== CONTACT ===== */
        .contact-section {
            padding: 70px 0;
            background: #ffffff;
        }
        .contact-box {
            background: #f8faff;
            border-radius: 24px;
            padding: 36px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        .form-input {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            padding: 14px 18px;
            border-radius: 14px;
            width: 100%;
            font-size: 0.9rem;
            transition: 0.3s ease;
            color: #1a2332;
            font-weight: 500;
        }
        .form-input::placeholder { color: #94a3b8; font-weight: 400; }
        .form-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37,99,235,0.08);
            transform: scale(1.01);
        }
        .form-input.error { border-color: #dc2626; }
        .error-msg { color: #dc2626; font-size: 0.8rem; margin-top: 4px; display: none; font-weight: 600; }
        .error-msg.show { display: block; }
        .success-msg {
            background: #22c55e;
            color: #fff;
            padding: 12px 18px;
            border-radius: 14px;
            margin-top: 14px;
            display: none;
            font-weight: 600;
            animation: fadeInUp 0.5s ease;
        }
        .success-msg.show { display: block; }

        .btn-kirim {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 12px 32px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        .btn-kirim:hover {
            background: #1d4ed8;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.35);
        }
        .btn-kirim:active {
            transform: scale(0.95);
        }
        .btn-kirim::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent 30%,
                rgba(255, 255, 255, 0.15) 50%,
                transparent 70%
            );
            background-size: 200% 200%;
            animation: shimmer 3s infinite;
            pointer-events: none;
        }
        .btn-kirim:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .contact-info-item {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #f8faff;
            padding: 16px 20px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            margin-bottom: 14px;
            transition: all 0.3s ease;
        }
        .contact-info-item:hover {
            transform: translateX(5px);
            border-color: #2563eb;
        }
        .contact-info-item .icon {
            width: 44px;
            height: 44px;
            background: #e8effa;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563eb;
            font-size: 1.1rem;
        }
        .contact-info-item .label {
            font-weight: 700;
            color: #0b1a33;
            font-size: 0.9rem;
        }
        .contact-info-item .value {
            color: #475569;
            font-size: 0.85rem;
        }

        /* ===== FOOTER ===== */
        footer {
            background: #ffffff;
            border-top: 2px solid #e2e8f0;
            padding: 28px 0;
            text-align: center;
        }
        footer .footer-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        footer .footer-logo img {
            height: 36px;
            width: auto;
            object-fit: contain;
        }
        footer .footer-logo .footer-text {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-style: italic;
            font-size: 1.3rem;
            color: #0b1a33;
        }
        footer p {
            color: #94a3b8;
            font-size: 0.85rem;
            margin-top: 6px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .feature-grid { grid-template-columns: 1fr; }
            .feature-card-large { grid-row: span 1; }
            .hero { min-height: 60vh; }
            .section-title { font-size: 1.9rem; }
            .contact-box { padding: 24px; }
            .logo-img { height: 38px; }
            footer .footer-logo img { height: 28px; }
            footer .footer-logo .footer-text { font-size: 1.1rem; }
        }
    </style>
</head>
<body>

<!-- ===== NAVBAR ===== -->
<nav class="navbar fixed top-0 w-full z-50">
    <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
        <a href="#" class="flex items-center gap-3">
            <img 
                src="{{ asset('logos/e library.png') }}" 
                alt="E-Library" 
                class="logo-img logo-anim"
                onerror="this.onerror=null; this.src='logos/e library.png';"
            />
            <span class="art-title text-indigo-700 text-xl" style="display:none;" id="logoFallback">E-Library</span>
        </a>

        <div class="hidden md:flex items-center gap-6 text-sm text-slate-600 font-semibold">
            <a href="#fitur" class="hover:text-indigo-600 transition">Fitur</a>
            <a href="#kontak" class="hover:text-indigo-600 transition">Kontak</a>
            <a href="{{ route('login') }}" class="btn-masuk">Masuk</a>
        </div>

        <button id="menuBtn" class="md:hidden text-slate-600 text-xl">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    <div id="mobileMenu" class="hidden md:hidden bg-white border-t-2 border-slate-100 px-4 py-3 flex flex-col gap-2 text-sm font-semibold">
        <a href="#fitur" class="text-slate-600 hover:text-indigo-600 py-2">Fitur</a>
        <a href="#kontak" class="text-slate-600 hover:text-indigo-600 py-2">Kontak</a>
        <a href="{{ route('login') }}" class="btn-masuk text-center">Masuk</a>
    </div>
</nav>

<!-- ===== HERO ===== -->
<section class="hero">
    <img class="hero-bg" src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=1200&q=80" alt="Perpustakaan" />
    <div class="hero-overlay"></div>

    <div class="hero-content max-w-6xl mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="anim-fade-up">
                <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur px-5 py-2 rounded-full text-xs font-semibold text-white border border-white/20 mb-5">
                    <i class="fas fa-graduation-cap"></i> Perpustakaan Digital
                </div>
                <h1 class="art-title text-5xl sm:text-6xl text-white leading-tight">
                    E-Library
                    <span class="block text-2xl text-white/80 font-normal mt-2">baca · pinjam · kelola</span>
                </h1>
                <p class="text-white/90 text-base mt-4 max-w-md font-medium">
                    Platform modern untuk manajemen perpustakaan digital. Temukan, pinjam, dan pantau koleksi buku dengan mudah.
                </p>
            </div>

            <div class="hidden md:flex justify-center anim-fade-up delay-2">
                <div class="w-64 h-64 rounded-full border-2 border-white/20 flex items-center justify-center">
                    <div class="w-48 h-48 rounded-full border-2 border-white/10 flex items-center justify-center">
                        <div class="w-32 h-32 rounded-full bg-white/5 flex items-center justify-center">
                            <i class="fas fa-book-open text-5xl text-white/20"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="scroll-indicator" style="position:absolute;bottom:20px;left:50%;transform:translateX(-50%);color:rgba(255,255,255,0.3);font-size:0.6rem;letter-spacing:0.1em;z-index:2;">
        <div style="width:1px;height:28px;background:rgba(255,255,255,0.2);margin:0 auto 4px;"></div>
        GULIR
    </div>
</section>

<!-- ===== FITUR ===== -->
<section id="fitur" class="feature-section px-4">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col gap-1 anim-fade-up">
            <span class="section-label">Fitur</span>
            <h2 class="section-title">Lebih dari sekadar <span>perpustakaan</span></h2>
            <p class="text-slate-400 text-sm font-medium">Dirancang untuk kemudahan dan pengalaman terbaik</p>
        </div>

        <div class="feature-grid">
            <!-- CARD 1 -->
            <div class="feature-card feature-card-large anim-fade-up delay-1">
                <div class="feature-number">01</div>
                <h3 class="feature-title">Katalog Digital</h3>
                <p class="feature-desc">Cari buku berdasarkan judul, penulis, atau kategori dengan cepat dan akurat. Tersedia 24/7.</p>
                <div class="feature-tags">
                    <span><i class="fas fa-circle-check"></i> Realtime</span>
                    <span><i class="fas fa-circle-check"></i> Akurat</span>
                </div>
            </div>

            <!-- CARD 2 -->
            <div class="feature-card anim-fade-up delay-2">
                <div class="feature-number">02</div>
                <h3 class="feature-title">Peminjaman Instan</h3>
                <p class="feature-desc">Proses pinjam 1 klik, durasi 7 hari dengan pengingat otomatis.</p>
            </div>

            <!-- CARD 3 -->
            <div class="feature-card anim-fade-up delay-3">
                <div class="feature-number">03</div>
                <h3 class="feature-title">Laporan & Statistik</h3>
                <p class="feature-desc">Pantau sirkulasi buku dan riwayat peminjaman secara realtime.</p>
                <div class="feature-tags">
                    <span><i class="fas fa-chart-line"></i> Visualisasi</span>
                    <span><i class="fas fa-clock"></i> Realtime</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== KONTAK ===== -->
<section id="kontak" class="contact-section px-4">
    <div class="max-w-6xl mx-auto">
        <div class="grid md:grid-cols-5 gap-8 items-start">
            <div class="md:col-span-2 anim-fade-up">
                <span class="section-label">Kontak</span>
                <h2 class="section-title">Ada <span>pertanyaan</span>?</h2>
                <p class="text-slate-400 text-sm font-medium mt-1 mb-5">Tim kami siap membantu Anda</p>

                <div class="contact-info-item">
                    <div class="icon"><i class="fas fa-map-pin"></i></div>
                    <div>
                        <div class="label">Politeknik Negeri Batam</div>
                        <div class="value">Jl. Ahmad Yani, Batam</div>
                    </div>
                </div>
                <div class="contact-info-item">
                    <div class="icon"><i class="fas fa-envelope"></i></div>
                    <div>
                        <div class="label">Email</div>
                        <div class="value">if-2ma-02@polibatam.ac.id</div>
                    </div>
                </div>
            </div>

            <div class="md:col-span-3 anim-fade-up delay-2">
                <div class="contact-box">
                    <form id="contactForm" novalidate>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <input type="text" id="nama" placeholder="Nama lengkap" class="form-input" required />
                                <div class="error-msg" id="namaError">Nama wajib diisi</div>
                            </div>
                            <div>
                                <input type="email" id="email" placeholder="Alamat email" class="form-input" />
                                <div class="error-msg" id="emailError">Email tidak valid</div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <textarea id="pesan" rows="4" placeholder="Tulis pesan..." class="form-input" required></textarea>
                            <div class="error-msg" id="pesanError">Pesan wajib diisi</div>
                        </div>
                        <button type="submit" class="btn-kirim inline-flex items-center gap-2 mt-4">
                            <i class="fas fa-paper-plane"></i> Kirim Pesan
                        </button>
                        <div class="success-msg" id="successMsg">✅ Pesan terkirim!</div>
                    </form>
                    <p class="text-slate-400 text-xs font-medium mt-3">* Kami balas dalam 1x24 jam</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<footer>
    <div class="footer-logo">
        <img 
            src="{{ asset('logos/e library.png') }}" 
            alt="E-Library" 
            onerror="this.style.display='none'; document.getElementById('footerFallback').style.display='block';"
        />
        <span class="footer-text" id="footerFallback" style="display:none;">E-Library</span>
    </div>
    <p>© 2026 · Politeknik Negeri Batam</p>
</footer>

<!-- ===== SCRIPT ===== -->
<script>
    // Mobile menu
    document.getElementById('menuBtn').addEventListener('click', function() {
        document.getElementById('mobileMenu').classList.toggle('hidden');
    });

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(function(a) {
        a.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
                document.getElementById('mobileMenu').classList.add('hidden');
            }
        });
    });

    // Cek apakah logo muncul
    document.addEventListener('DOMContentLoaded', function() {
        const logo = document.querySelector('.logo-img');
        if (logo) {
            logo.addEventListener('error', function() {
                this.style.display = 'none';
                document.getElementById('logoFallback').style.display = 'block';
            });
        }
    });

    // Form validation
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const nama = document.getElementById('nama');
        const email = document.getElementById('email');
        const pesan = document.getElementById('pesan');
        let valid = true;

        document.querySelectorAll('.error-msg').forEach(el => el.classList.remove('show'));
        document.querySelectorAll('.form-input').forEach(el => el.classList.remove('error'));
        document.getElementById('successMsg').classList.remove('show');

        if (!nama.value.trim()) {
            nama.classList.add('error');
            document.getElementById('namaError').classList.add('show');
            valid = false;
        }
        if (email.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
            email.classList.add('error');
            document.getElementById('emailError').classList.add('show');
            valid = false;
        }
        if (!pesan.value.trim()) {
            pesan.classList.add('error');
            document.getElementById('pesanError').classList.add('show');
            valid = false;
        }

        if (valid) {
            const btn = this.querySelector('.btn-kirim');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
            btn.disabled = true;

            setTimeout(() => {
                document.getElementById('successMsg').innerHTML = '✅ Terima kasih ' + nama.value + '! Pesan terkirim.';
                document.getElementById('successMsg').classList.add('show');
                btn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Pesan';
                btn.disabled = false;
                this.reset();
                document.querySelectorAll('.form-input').forEach(el => el.classList.remove('error'));
                
                setTimeout(() => {
                    document.getElementById('successMsg').classList.remove('show');
                }, 5000);
            }, 1500);
        }
    });
</script>
</body>
</html>