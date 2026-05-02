<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <metaname="viewport"content="width=device-width, initial-scale=1.0, user-scalable=yes"/>
    <title>E-Library | Sistem Perpustakaan Digital Modern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap"rel="stylesheet"/>
    <linkrel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  </head>
  <body>
    <nav class="navbar">
      <div class="nav-container">
        <a href="#" class="logo"
          ><i class="fas fa-landmark"></i><span>E-Library</span></a
        >
        <button class="menu-toggle" id="menuToggle">
          <i class="fas fa-bars"></i>
        </button>
        <div class="nav-links" id="navLinks">
          <a href="#fitur">Fitur</a>
          <a href="#tentang">Tim Pengembang</a>
          <a href="#kontak">Kontak</a>
          <a href="{{route('login')}}" class="btn-login-nav"
            ><i class="fas fa-sign-in-alt"></i> Masuk ke Aplikasi</a
          >
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="hero">
      <div class="hero-container">
        <div class="hero-content">
          <div class="access-badge">
            <i class="fas fa-graduation-cap"></i> Sistem Perpustakaan Digital
          </div>
          <h1>E-Library</h1>
          <p>
            Platform manajemen perpustakaan digital modern. Kelola peminjaman,
            stok buku, dan anggota dengan mudah.
          </p>
          <div class="hero-buttons">
            <a href="{{route('login')}}" class="btn-primary-lg"
              ><i class="fas fa-arrow-right"></i> Masuk ke Aplikasi</a
            >
            <a href="#fitur" class="btn-outline-lg"
              ><i class="fas fa-info-circle"></i> Lihat Fitur</a
            >
          </div>
        </div>
      </div>
    </section>

    <!-- Fitur Section -->
    <section id="fitur" class="section">
      <div class="container">
        <h2 class="section-title">Fitur Unggulan</h2>
        <p class="section-subtitle">
          Kemampuan canggih yang memudahkan pengelolaan perpustakaan
        </p>
        <div class="features-grid">
          <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-book-open"></i></div>
            <h3>Katalog Buku Digital</h3>
            <p>
              Pencarian buku berdasarkan judul, penulis, kategori, dan stok
              realtime.
            </p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-hand-holding-heart"></i>
            </div>
            <h3>Peminjaman Instan</h3>
            <p>
              Pinjam buku dengan satu klik, durasi 7 hari dengan pengingat
              otomatis.
            </p>
          </div>
          <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-users"></i></div>
            <h3>Kelola Anggota</h3>
            <p>
              Admin dapat menambah, mengedit, menghapus dan melihat seluruh
              anggota perpustakaan.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Tim Pengembang Section -->
    <section id="tentang" class="section" style="background: var(--light-gray)">
      <div class="container">
        <h2 class="section-title">👨‍💻 Tim Pengembang</h2>
        <p class="section-subtitle">
          Dibangun oleh tim kreatif dan berdedikasi
        </p>
        <div class="team-grid">
          <div class="team-card">
            <div class="team-avatar"><i class="fas fa-user-tie"></i></div>
            <h3 class="team-name">M. Fahreza R.</h3>
            <p class="team-role">Project Manager & Fullstack Dev</p>
            <p class="team-bio">
              Berpengalaman dalam pengembangan web fullstack dengan fokus pada
              performa dan keamanan aplikasi.
            </p>
            <div class="team-social">
              <a href="#"><i class="fab fa-github"></i></a>
              <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
          </div>
          <div class="team-card">
            <div class="team-avatar"><i class="fas fa-laptop-code"></i></div>
            <h3 class="team-name">Aulia S. Putri</h3>
            <p class="team-role">UI/UX Designer & Frontend</p>
            <p class="team-bio">
              Mendesain antarmuka yang intuitif dan pengalaman pengguna yang
              menyenangkan.
            </p>
            <div class="team-social">
              <a href="#"><i class="fab fa-dribbble"></i></a>
              <a href="#"><i class="fab fa-behance"></i></a>
            </div>
          </div>
          <div class="team-card">
            <div class="team-avatar"><i class="fas fa-database"></i></div>
            <h3 class="team-name">Rizki Hidayat</h3>
            <p class="team-role">Backend Developer</p>
            <p class="team-bio">
              Mengembangkan sistem backend yang handal, scalable, dan aman untuk
              aplikasi perpustakaan.
            </p>
            <div class="team-social">
              <a href="#"><i class="fab fa-github"></i></a>
              <a href="#"><i class="fab fa-stack-overflow"></i></a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Kontak Section -->
    <section id="kontak" class="section">
      <div class="container">
        <h2 class="section-title">📞 Hubungi Kami</h2>
        <p class="section-subtitle">
          Punya pertanyaan? Tim kami siap membantu.
        </p>
        <div class="contact-grid">
          <div class="contact-info">
            <div class="contact-item">
              <div class="contact-icon">
                <i class="fas fa-map-marker-alt"></i>
              </div>
              <div class="contact-text">
                <h4>Alamat Kantor</h4>
                <p>Jl. Teknologi No. 123, Bandung, Jawa Barat</p>
              </div>
            </div>
            <div class="contact-item">
              <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
              <div class="contact-text">
                <h4>Telepon / WA</h4>
                <p>+62 812 3456 7890</p>
              </div>
            </div>
            <div class="contact-item">
              <div class="contact-icon"><i class="fas fa-envelope"></i></div>
              <div class="contact-text">
                <h4>Email Resmi</h4>
                <p>support@elibrary.id</p>
              </div>
            </div>
          </div>
          <div class="contact-form">
            <form id="contactForm">
              <div class="form-group">
                <input
                  type="text"
                  id="contactName"
                  placeholder="Nama Lengkap"
                  required
                />
              </div>
              <div class="form-group">
                <input
                  type="email"
                  id="contactEmail"
                  placeholder="Email Aktif"
                  required
                />
              </div>
              <div class="form-group">
                <textarea
                  id="contactMessage"
                  rows="4"
                  placeholder="Tulis pesan Anda..."
                  required
                ></textarea>
              </div>
              <button type="submit" class="btn-submit">
                <i class="fas fa-paper-plane"></i> Kirim Pesan
              </button>
            </form>
            <p
              style="
                font-size: 0.7rem;
                color: var(--gray);
                margin-top: 1rem;
                text-align: center;
              "
            >
              * Kami akan membalas dalam 1x24 jam
            </p>
          </div>
        </div>
      </div>
    </section>

    <footer class="footer">
      <div class="footer-content">
        <div class="logo" style="justify-content: center">
          <i class="fas fa-landmark"></i
          ><span style="color: white">E-Library</span>
        </div>
        <p>
          Sistem Perpustakaan Digital Terintegrasi | Copyright © 2026 E-Library
          Team
        </p>
      </div>
    </footer>

    <script>
      // Mobile menu toggle
      const menuToggle = document.getElementById("menuToggle");
      const navLinks = document.getElementById("navLinks");
      menuToggle?.addEventListener("click", () => {
        navLinks.classList.toggle("active");
      });

      // Smooth scroll & close mobile menu
      document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener("click", function (e) {
          const targetId = this.getAttribute("href");
          if (targetId === "#") return;
          const target = document.querySelector(targetId);
          if (target) {
            e.preventDefault();
            target.scrollIntoView({behavior: "smooth"});
            navLinks.classList.remove("active");
          }
        });
      });

      // Contact form
      document
        .getElementById("contactForm")
        ?.addEventListener("submit", function (e) {
          e.preventDefault();
          const name = document.getElementById("contactName").value;
          if (name) {
            alert(`✅ Terima kasih ${name}, pesan Anda telah terkirim!`);
            this.reset();
          } else alert("❌ Harap lengkapi data.");
        });
    </script>
  </body>
</div>
</html>
