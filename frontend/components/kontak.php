<section id="kontak" class="contact-section">
    <?php if (!empty($_SESSION['contact_flash'])): ?>
        <?php $contactFlash = $_SESSION['contact_flash']; unset($_SESSION['contact_flash']); ?>
        <div class="contact-feedback contact-feedback-<?= htmlspecialchars($contactFlash['type'], ENT_QUOTES, 'UTF-8'); ?>">
            <?= htmlspecialchars($contactFlash['message'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>
    <div class="section-title">
        <span class="section-tag">
            KONTAK & LOKASI
        </span>
        <h2>Hubungi Kami</h2>
        <p>
            Silakan hubungi Humas SMK untuk informasi kemitraan,
            PKL, atau pertanyaan seputar sekolah.
        </p>
    </div>
    <div class="contact-container">
        <!-- =========================
             INFORMASI KONTAK
        ========================== -->
        <div class="contact-info-card">
            <div class="info-item">

                <div class="info-icon">
                    <i class="fa-solid fa-location-dot"></i>
                </div>

                <div class="info-text">

                    <h3>Alamat Kantor</h3>

                    <p>
                        Jl. Contoh No. 123, Kabupaten/Kota,<br>
                        Jawa Barat, Indonesia
                    </p>

                </div>

            </div>


            <div class="info-item">

                <div class="info-icon">
                    <i class="fa-solid fa-phone"></i>
                </div>

                <div class="info-text">

                    <h3>Telepon & WhatsApp</h3>

                    <p>
                        0812-3456-7890 / (021) 555-0123
                    </p>

                </div>

            </div>


            <div class="info-item">

                <div class="info-icon">
                    <i class="fa-solid fa-envelope"></i>
                </div>

                <div class="info-text">

                    <h3>Email Resmi</h3>

                    <p>
                        humas@smk.sch.id
                    </p>

                </div>

            </div>


            <div class="info-item">

                <div class="info-icon">
                    <i class="fa-solid fa-clock"></i>
                </div>

                <div class="info-text">

                    <h3>Jam Pelayanan</h3>

                    <p>
                        Senin - Jumat: 07.00 - 15.00 WIB<br>
                        (Sabtu & Minggu Libur)
                    </p>

                </div>

            </div>

        </div>



        <!-- =========================
             FORM KONTAK
        ========================== -->

        <div class="contact-form-card">

            <h3>Kirim Pesan Langsung</h3>


            <form
                class="contact-form"
                action="components/_kontak.php"
                method="POST"
            >


                <!-- NAMA -->

                <div class="form-group">

                    <label for="nama">
                        Nama Lengkap
                    </label>

                    <div class="input-with-icon">

                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            placeholder="Masukkan nama Anda"
                            required
                        >

                        <i class="fa-solid fa-user"></i>

                    </div>

                </div>



                <!-- EMAIL -->

                <div class="form-group">

                    <label for="email">
                        Alamat Email
                    </label>

                    <div class="input-with-icon">

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="nama@email.com"
                            required
                        >

                        <i class="fa-solid fa-envelope"></i>

                    </div>

                </div>



                <!-- SUBJEK -->

                <div class="form-group">

                    <label for="subjek">
                        Subjek Pesan
                    </label>

                    <div class="input-with-icon">

                        <input
                            type="text"
                            id="subjek"
                            name="subjek"
                            placeholder="Contoh: Kemitraan PKL"
                            required
                        >

                        <i class="fa-solid fa-tag"></i>

                    </div>

                </div>



                <!-- PESAN -->

                <div class="form-group">

                    <label for="pesan">
                        Pesan Anda
                    </label>

                    <textarea
                        id="pesan"
                        name="pesan"
                        placeholder="Tuliskan pesan atau pertanyaan Anda di sini..."
                        required
                    ></textarea>

                </div>



                <!-- TOMBOL -->

                <button
                    type="submit"
                    class="btn"
                >

                    <i class="fa-solid fa-paper-plane"></i>

                    Kirim Pesan

                </button>


            </form>

        </div>

    </div>

</section>