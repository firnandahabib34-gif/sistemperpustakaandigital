<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Library | Sistem Perpustakaan Digital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) 
    <!-- Tailwind CSS CDN -->
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="font-sans bg-white">

<!-- Navbar -->
<nav class="fixed top-0 w-full bg-white/95 backdrop-blur-md shadow-sm z-50">
    <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
        <a href="#" class="flex items-center gap-2 text-xl font-bold text-indigo-600">
            <i class="fas fa-landmark text-2xl"></i>
            <span>E-Library</span>
        </a>
        
        <div class="hidden md:flex items-center gap-6">
            <a href="#fitur" class="text-gray-600 hover:text-indigo-600 transition">Fitur</a>
            <a href="#tentang" class="text-gray-600 hover:text-indigo-600 transition">Tim Pengembang</a>
            <a href="#kontak" class="text-gray-600 hover:text-indigo-600 transition">Kontak</a>
            <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-5 py-2 rounded-full hover:bg-indigo-700 transition">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </a>
        </div>
        
        <!-- Mobile menu button -->
        <button id="menuBtn" class="md:hidden text-gray-600 text-2xl">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <!-- Mobile menu -->
    <div id="mobileMenu" class="hidden md:hidden bg-white border-t px-4 py-3 flex flex-col gap-3">
        <a href="#fitur" class="text-gray-600 hover:text-indigo-600 py-2">Fitur</a>
        <a href="#tentang" class="text-gray-600 hover:text-indigo-600 py-2">Tim Pengembang</a>
        <a href="#kontak" class="text-gray-600 hover:text-indigo-600 py-2">Kontak</a>
        <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-5 py-2 rounded-full text-center hover:bg-indigo-700">
            Masuk
        </a>
    </div>
</nav>

<!-- Hero Section -->
<section class="pt-32 pb-20 px-4 bg-gradient-to-br from-indigo-50 to-white">
    <div class="max-w-4xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 bg-indigo-100 text-indigo-700 px-4 py-2 rounded-full text-sm mb-6">
            <i class="fas fa-graduation-cap"></i>
            <span>Sistem Perpustakaan Digital</span>
        </div>
        <h1 class="text-5xl md:text-6xl font-bold text-gray-800 mb-4">E-Library</h1>
        <p class="text-lg text-gray-500 mb-8 max-w-2xl mx-auto">
            Platform manajemen perpustakaan digital modern. Kelola peminjaman, stok buku, dan anggota dengan mudah.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-8 py-3 rounded-full font-semibold hover:bg-indigo-700 transition inline-flex items-center justify-center gap-2">
                <i class="fas fa-arrow-right"></i> Masuk ke Aplikasi
            </a>
            <a href="#fitur" class="border-2 border-indigo-600 text-indigo-600 px-8 py-3 rounded-full font-semibold hover:bg-indigo-600 hover:text-white transition inline-flex items-center justify-center gap-2">
                <i class="fas fa-info-circle"></i> Lihat Fitur
            </a>
        </div>
    </div>
</section>

<!-- Fitur Section -->
<section id="fitur" class="py-20 px-4 bg-white">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Fitur Unggulan</h2>
            <p class="text-gray-500">Kemampuan canggih yang memudahkan pengelolaan perpustakaan</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-lg transition text-center border border-gray-100">
                <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-book-open text-indigo-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Katalog Buku Digital</h3>
                <p class="text-gray-500">Pencarian buku berdasarkan judul, penulis, kategori, dan stok realtime.</p>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-lg transition text-center border border-gray-100">
                <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-hand-holding-heart text-indigo-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Peminjaman Instan</h3>
                <p class="text-gray-500">Pinjam buku dengan satu klik, durasi 7 hari dengan pengingat otomatis.</p>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-lg transition text-center border border-gray-100">
                <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-indigo-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Kelola Anggota</h3>
                <p class="text-gray-500">Admin dapat menambah, mengedit, menghapus dan melihat seluruh anggota perpustakaan.</p>
            </div>
        </div>
    </div>
</section>

<!-- Tim Pengembang Section -->
<section id="tentang" class="py-20 px-4 bg-gray-50">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">👨‍💻 Tim Pengembang</h2>
            <p class="text-gray-500">Dibangun oleh tim kreatif dan berdedikasi</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-md text-center">
                <div class="w-24 h-24 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 text-white text-3xl">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h3 class="text-lg font-bold">M. Fahreza R.</h3>
                <p class="text-indigo-600 text-sm mb-2">Project Manager & Fullstack Dev</p>
                <p class="text-gray-500 text-sm">Berpengalaman dalam pengembangan web fullstack dengan fokus pada performa dan keamanan aplikasi.</p>
                <div class="flex justify-center gap-3 mt-4 text-gray-400">
                    <a href="#" class="hover:text-indigo-600"><i class="fab fa-github text-lg"></i></a>
                    <a href="#" class="hover:text-indigo-600"><i class="fab fa-linkedin text-lg"></i></a>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-md text-center">
                <div class="w-24 h-24 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 text-white text-3xl">
                    <i class="fas fa-laptop-code"></i>
                </div>
                <h3 class="text-lg font-bold">Aulia S. Putri</h3>
                <p class="text-indigo-600 text-sm mb-2">UI/UX Designer & Frontend</p>
                <p class="text-gray-500 text-sm">Mendesain antarmuka yang intuitif dan pengalaman pengguna yang menyenangkan.</p>
                <div class="flex justify-center gap-3 mt-4 text-gray-400">
                    <a href="#" class="hover:text-indigo-600"><i class="fab fa-dribbble text-lg"></i></a>
                    <a href="#" class="hover:text-indigo-600"><i class="fab fa-behance text-lg"></i></a>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-md text-center">
                <div class="w-24 h-24 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 text-white text-3xl">
                    <i class="fas fa-database"></i>
                </div>
                <h3 class="text-lg font-bold">Rizki Hidayat</h3>
                <p class="text-indigo-600 text-sm mb-2">Backend Developer</p>
                <p class="text-gray-500 text-sm">Mengembangkan sistem backend yang handal, scalable, dan aman untuk aplikasi perpustakaan.</p>
                <div class="flex justify-center gap-3 mt-4 text-gray-400">
                    <a href="#" class="hover:text-indigo-600"><i class="fab fa-github text-lg"></i></a>
                    <a href="#" class="hover:text-indigo-600"><i class="fab fa-stack-overflow text-lg"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Kontak Section -->
<section id="kontak" class="py-20 px-4 bg-white">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">📞 Hubungi Kami</h2>
            <p class="text-gray-500">Punya pertanyaan? Tim kami siap membantu.</p>
        </div>
        
        <div class="grid md:grid-cols-2 gap-8">
            <div class="bg-gray-50 p-6 rounded-2xl">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-indigo-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold">Alamat Kantor</h4>
                        <p class="text-gray-500 text-sm">Jl. Teknologi No. 123, Bandung, Jawa Barat</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-phone-alt text-indigo-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold">Telepon / WA</h4>
                        <p class="text-gray-500 text-sm">+62 812 3456 7890</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-envelope text-indigo-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold">Email Resmi</h4>
                        <p class="text-gray-500 text-sm">support@elibrary.id</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 p-6 rounded-2xl">
                <form id="contactForm">
                    <div class="mb-4">
                        <input type="text" id="contactName" placeholder="Nama Lengkap" class="w-full p-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div class="mb-4">
                        <input type="email" id="contactEmail" placeholder="Email Aktif" class="w-full p-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div class="mb-4">
                        <textarea id="contactMessage" rows="4" placeholder="Tulis pesan Anda..." class="w-full p-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-semibold hover:bg-indigo-700 transition flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i> Kirim Pesan
                    </button>
                </form>
                <p class="text-center text-gray-400 text-xs mt-4">* Kami akan membalas dalam 1x24 jam</p>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-gray-400 py-8 px-4 text-center">
    <div class="flex items-center justify-center gap-2 mb-3">
        <i class="fas fa-landmark text-indigo-400 text-xl"></i>
        <span class="text-white font-bold">E-Library</span>
    </div>
    <p class="text-sm">Sistem Perpustakaan Digital Terintegrasi | Copyright © 2026 E-Library Team</p>
</footer>

<script>
    // Mobile menu toggle
    const menuBtn = document.getElementById('menuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (menuBtn) {
        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }
    
    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
                mobileMenu?.classList.add('hidden');
            }
        });
    });
    
    // Contact form
    document.getElementById('contactForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('contactName').value;
        if (name) {
            alert(`✅ Terima kasih ${name}, pesan Anda telah terkirim!`);
            this.reset();
        } else {
            alert('❌ Harap lengkapi data.');
        }
    });
</script>

</body>
</html>