<?php
include 'includes/header.php';
include 'config/db.php';

// Ambil data produk miniatur
$produk = mysqli_query($conn, "SELECT * FROM produk");
?>
<link href="assets/css/output.css" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<style>
    /* CSS untuk menyembunyikan teks di hero section sebelum animasi JS */
    #animatedHeading,
    #animatedParagraph {
        visibility: hidden;
    }

    /* CSS untuk kursor berkedip (opsional, untuk efek mengetik) */
    .blinking-cursor {
        font-weight: 100;
        opacity: 1;
        animation: blink .7s infinite;
    }

    @keyframes blink {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0;
        }

        100% {
            opacity: 1;
        }
    }
</style>

<section class="relative h-[400px] bg-cover flex items-center justify-center"
    style="background-image: url('/penjualan-miniatur/assets/img/banner.png');">
    <div class="absolute inset-0 opacity-40"></div>

    <div class="relative z-10 text-center text-white px-4">
        <h1 class="text-4xl md:text-5xl font-bold mb-4" id="animatedHeading">Selamat Datang di Toko Miniatur</h1>
        <p class="text-lg md:text-xl max-w-2xl mx-auto" id="animatedParagraph">
            Kami menjual miniatur kendaraan, bangunan, dan bentuk kreatif lainnya yang dibuat secara detail dan presisi.
            Temukan koleksi terbaik untuk melengkapi hobi dan pajangan Anda.
            <br><br>
            🖌 Custom Diorama ✂ Pembuatan Diorama
            <br>
            📩 Hubungi untuk pesanan kustom dan kerjasama:
            <a href="https://wa.me/6289538857913?text=Halo%20Admin%2C%20saya%20tertarik%20ingin%20custom%20miniatur"
                target="_blank" class="text-green-400 underline hover:text-green-500">
                📞088200005772
            </a>
        </p>
    </div>
</section>

<main class="container mx-auto px-4 py-10 space-y-16">

    <section class="bg-white shadow-md rounded-lg p-6 md:p-10" data-aos="fade-up">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold inline-flex items-center gap-2 border-b-2 border-blue-600 pb-2">
                👤 Kata Sambutan
            </h2>
        </div>
        <p class="text-gray-700 text-lg leading-relaxed">
            Selamat datang di website resmi <strong>Toko Miniatur</strong>. Kami sangat senang bisa menjadi bagian dari hobi dan passion Anda.
            Kami percaya bahwa detail kecil membuat perbedaan besar, dan semangat itulah yang mendorong kami untuk terus menghadirkan produk miniatur berkualitas.
            <br><br>
            Harapan kami, Toko Miniatur bisa menjadi tempat terbaik untuk memenuhi keinginan Anda dalam mengoleksi karya miniatur yang unik dan personal.
        </p>
    </section>

    <section class="bg-gray-50 shadow-md rounded-lg p-6 md:p-10" data-aos="fade-up">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold inline-flex items-center gap-2 border-b-2 border-blue-600 pb-2">
                🏢 Tentang Kami
            </h2>
        </div>
        <p class="text-gray-700 text-lg leading-relaxed">
            <strong>Toko Miniatur</strong> didirikan pada tahun <strong>2022</strong> oleh sekelompok pengrajin yang memiliki kecintaan mendalam terhadap dunia miniatur.
            Dengan latar belakang seni rupa dan teknik manufaktur skala kecil, kami menghadirkan miniatur yang tidak hanya estetis,
            tetapi juga presisi secara proporsi dan detail.
            <br><br>
            Perusahaan kami berbadan hukum <strong>CV (Commanditaire Vennootschap)</strong> dan memiliki tujuan utama untuk menyediakan miniatur berkualitas tinggi
            bagi pecinta koleksi, dekorasi interior, dan hadiah personal yang bernilai.
        </p>
    </section>

    <section class="bg-white shadow-md rounded-lg p-6 md:p-10" data-aos="fade-up">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold inline-flex items-center gap-2 border-b-2 border-blue-600 pb-2">
                🎯 Visi & Misi
            </h2>
        </div>
        <div class="grid md:grid-cols-2 gap-8 text-gray-700 text-lg">
            <div class="bg-blue-50 p-6 rounded-lg shadow-sm">
                <h3 class="text-xl font-semibold mb-2">✨ Visi</h3>
                <p>
                    Menjadi toko miniatur terdepan di Indonesia yang menginspirasi kreativitas dan detail dalam setiap karya.
                </p>
            </div>
            <div class="bg-blue-50 p-6 rounded-lg shadow-sm">
                <h3 class="text-xl font-semibold mb-2">📌 Misi</h3>
                <ul class="list-disc list-inside space-y-1">
                    <li>Menghadirkan produk miniatur yang presisi dan berkualitas.</li>
                    <li>Melayani permintaan custom miniatur sesuai keinginan pelanggan.</li>
                    <li>Menjaga kepercayaan pelanggan dengan pelayanan profesional.</li>
                    <li>Mengembangkan komunitas kreatif pecinta miniatur di Indonesia.</li>
                </ul>
            </div>
        </div>
    </section>


    <section>
        <h2 class="text-2xl font-bold mb-6 text-center" data-aos="zoom-in">🛍️ Produk Miniatur Kami</h2>
        <?php if (mysqli_num_rows($produk) > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <?php while ($row = mysqli_fetch_assoc($produk)): ?>
                    <div class="bg-white shadow rounded-lg overflow-hidden transform hover:scale-105 transition duration-300" data-aos="fade-up">
                        <img src="uploads/produk/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama']) ?>" class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold"><?= htmlspecialchars($row['nama']) ?></h3>
                            <p class="text-gray-600 text-sm mb-2"><?= htmlspecialchars($row['deskripsi']) ?></p>
                            <p class="text-blue-600 font-bold">Rp <?= number_format($row['harga']) ?></p>
                            <a href="pages/login.php" class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Beli Sekarang</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500 text-center">Belum ada produk yang tersedia saat ini.</p>
        <?php endif; ?>
    </section>
</main>

<div id="lightboxModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-70 z-50 hidden">
    <span class="absolute top-5 right-5 text-white text-3xl font-bold cursor-pointer" onclick="closeModal()">&times;</span>
    <img id="lightboxImage" src="" alt="Enlarged Image" class="max-w-full max-h-full rounded">
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    // --- Fungsi Lightbox ---
    function openModal(src) {
        var modal = document.getElementById('lightboxModal');
        var image = document.getElementById('lightboxImage');
        image.src = src;
        modal.classList.remove('hidden');
    }

    function closeModal() {
        var modal = document.getElementById('lightboxModal');
        modal.classList.add('hidden');
    }

    // Menutup modal saat klik di luar gambar
    document.getElementById('lightboxModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // --- Inisialisasi AOS (untuk section lainnya) ---
    AOS.init();

    // --- Animasi Ketik Teks di Hero Section ---
    document.addEventListener('DOMContentLoaded', function() {
        const headingElement = document.getElementById('animatedHeading');
        const paragraphElement = document.getElementById('animatedParagraph');

        // Simpan teks asli dan kosongkan elemen untuk animasi
        const originalHeadingText = headingElement.textContent;
        headingElement.textContent = '';
        headingElement.style.visibility = 'visible'; // Jadikan visible untuk memulai animasi

        const originalParagraphHTML = paragraphElement.innerHTML; // Simpan HTML asli untuk link
        const paragraphTextOnly = paragraphElement.textContent; // Ambil teks murni dari paragraf
        paragraphElement.innerHTML = '';
        // Paragraf akan visibility: visible saat animasinya dimulai

        let i = 0;

        function typeHeading() {
            if (i < originalHeadingText.length) {
                headingElement.textContent += originalHeadingText.charAt(i);
                i++;
                setTimeout(typeHeading, 50); // Kecepatan ketik judul (ms per karakter)
            } else {
                // Setelah heading selesai, mulai animasi paragraf
                animateParagraph();
            }
        }

        let j = 0;

        function animateParagraph() {
            paragraphElement.style.visibility = 'visible'; // Jadikan paragraf terlihat
            let currentContent = '';
            // Regex untuk memisahkan teks dan tag HTML (br atau a)
            const parts = originalParagraphHTML.split(/(<br><br>|<a.*?<\/a>)/g);
            let charIndexInTextOnly = 0; // Melacak indeks karakter dalam teks murni

            function typeParagraphChar() {
                if (j < paragraphTextOnly.length) {
                    currentContent = '';
                    let tempCharIndex = 0;
                    for (let k = 0; k < parts.length; k++) {
                        if (parts[k].startsWith('<br>') || parts[k].startsWith('<a')) {
                            currentContent += parts[k]; // Tambahkan tag HTML langsung
                        } else {
                            // Hitung berapa karakter dari bagian teks ini yang sudah ditampilkan
                            if (tempCharIndex + parts[k].length <= j) {
                                currentContent += parts[k]; // Tambahkan seluruh bagian teks
                            } else {
                                currentContent += parts[k].substring(0, j - tempCharIndex); // Tambahkan sebagian teks
                            }
                            tempCharIndex += parts[k].length;
                        }
                    }
                    paragraphElement.innerHTML = currentContent + '<span class="blinking-cursor"></span>'; // Tambahkan kursor opsional
                    j++;
                    setTimeout(typeParagraphChar, 25); // Kecepatan ketik paragraf (ms per karakter)
                } else {
                    // Setelah paragraf selesai, hapus kursor jika ada dan kembalikan HTML asli
                    const cursor = paragraphElement.querySelector('.blinking-cursor');
                    if (cursor) cursor.remove();
                    paragraphElement.innerHTML = originalParagraphHTML; // Penting agar link aktif
                }
            }
            typeParagraphChar(); // Mulai mengetik paragraf
        }

        // Mulai animasi heading saat DOM selesai dimuat
        typeHeading();
    });
</script>

<?php include 'includes/footer.php'; ?>