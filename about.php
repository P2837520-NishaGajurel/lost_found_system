<?php
include 'includes/header.php';
?>
<style>
/* ABOUT PAGE PROFESSIONAL */
.about-page {
    margin-bottom: 20px;
}

.about-hero {
    text-align: center;
}

.about-hero h2 {
    font-size: 34px;
    margin-bottom: 10px;
    color: #1f4e79;
}

.about-hero p {
    max-width: 850px;
    margin: 0 auto;
    font-size: 16px;
}

/* FLEX SECTIONS */
.about-section {
    padding: 20px;
}

.about-flex {
    display: flex;
    gap: 30px;
    align-items: center;
}

.about-flex.reverse {
    flex-direction: row-reverse;
}

/* IMAGE */
.about-img {
    flex: 1;
}

.about-img img {
    width: 100%;
    height: 300px;
    object-fit: cover;
    border-radius: 12px;
}

/* TEXT */
.about-text {
    flex: 1;
}

.about-text h3 {
    margin-bottom: 12px;
    color: #1f4e79;
}

.about-text h4 {
    margin-top: 14px;
    margin-bottom: 6px;
    color: #333;
}

.about-text p {
    margin-bottom: 8px;
    font-size: 15px;
}

/* MOBILE */
@media (max-width: 768px) {
    .about-flex {
        flex-direction: column;
    }

    .about-flex.reverse {
        flex-direction: column;
    }

    .about-img img {
        height: 220px;
    }

    .about-hero h2 {
        font-size: 26px;
    }
}
</style>
<div class="about-page">

    <!-- HERO -->
    <div class="card about-hero">
        <h2>About Us</h2>
        <p>
            Welcome to the <strong>Lost &amp; Found Item Management System</strong> of 
            <strong>Prthvi Secondary High School</strong>. This platform helps students and staff
            report, track, and recover lost items efficiently using a centralized system.
        </p>
    </div>

    <!-- SCHOOL SECTION -->
    <div class="card about-section">
        <div class="about-flex">

            <div class="about-img">
                <img src="/lost_found_system/assets/images/school.jpg" alt="School Image">
            </div>

            <div class="about-text">
                <h3>About the School</h3>
                <p>
                    <strong>Prithvi Secondary High School</strong> is dedicated to academic excellence,
                    discipline, and student welfare. The school promotes the use of modern
                    digital technologies to improve administrative efficiency and enhance
                    student experience.
                </p>

                <h4>School Information</h4>
                <p><strong>Address:</strong> Bidur No. 5, Main Road, Nuwakot, Nepal</p>
                <p><strong>Phone:</strong> +977-1-4567890</p>
                <p><strong>Email:</strong> info@prithvihighschool.edu.np</p>
                <p><strong>Website:</strong> www.prithvihighschool.edu.np</p>
            </div>

        </div>
    </div>

    <!-- ADMIN SECTION -->
    <div class="card about-section">
        <div class="about-flex reverse">

            <div class="about-img">
                <img src="/lost_found_system/assets/images/admin.jpg" alt="Admin Image">
            </div>

            <div class="about-text">
                <h3>Administrator</h3>
                <p>
                    The Lost &amp; Found System is managed by the school administration to ensure
                    proper verification, transparency, and accountability in item handling.
                </p>

                <h4>Admin Details</h4>
                <p><strong>Name:</strong> System Administrator</p>
                <p><strong>Email:</strong> admin@prithvihighschool.edu.np</p>
                <p><strong>Contact:</strong> +977-9800000000</p>
                <p><strong>Room:</strong> Administration Office, Room 12</p>
                <p><strong>Address:</strong> Prthvi Secondary High School, Nuwakot, Nepal</p>
            </div>

        </div>
    </div>

    <!-- PURPOSE -->
    <div class="card">
        <h3>Purpose of the System</h3>
        <p>
            This system replaces traditional manual lost and found methods with a digital solution.
            It improves item visibility, reduces confusion, and increases the chances of recovering
            lost belongings quickly through automated matching and notifications.
        </p>
    </div>

</div>

<?php
include 'includes/footer.php';
?>