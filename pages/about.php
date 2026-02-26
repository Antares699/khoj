<?php require_once '../includes/header.php'; ?>

<div class="about-hero">
    <div class="container">
        <h1>We help you discover the best of Nepal.</h1>
        <p>Khoj connects people with great local businesses.</p>
    </div>
</div>

<div class="container" style="padding: 60px 20px;">
    <div class="about-grid">
        <div class="about-text">
            <h2>Our Story</h2>
            <p style="text-align: justify;">Founded in 2026, Khoj was built with a simple mission: to help people
                find great local businesses in
                Nepal. From busy city centers to remote mountain villages, we believe that every
                business has a story, and every customer deserves to find exactly what they are looking for.</p>
            <p style="text-align: justify;">Our platform empowers local business owners to showcase their services,
                while giving customers a trusted
                place to share their experiences through authentic reviews.</p>
        </div>
        <div class="about-stat-card">
            <h3>For Users</h3>
            <p>Discover trusted local businesses, read reviews, and share your own experiences.</p>
            <a href="<?php echo $pagePrefix; ?>register.php" class="link-cta"
                style="background:var(--blue); display:inline-block; margin-top:10px;">Join Now</a>
        </div>
    </div>

    <div class="about-grid" style="margin-top: 60px;">
        <div class="about-stat-card" style="background: #fdf2f8;">
            <h3 style="color: #be185d;">For Businesses</h3>
            <p>Claim your business page, manage your reputation, and grow your customer base.</p>
            <a href="<?php echo $pagePrefix; ?>claim.php" class="link-cta"
                style="background:#be185d; display:inline-block; margin-top:10px;">Claim Business</a>
        </div>
        <div class="about-text">
            <h2>Why Khoj?</h2>
            <ul style="list-style: none; padding: 0;">
                <li style="margin-bottom: 12px; font-weight: 500;">✓ Verified Local Reviews</li>
                <li style="margin-bottom: 12px; font-weight: 500;">✓ Dedicated Business Tools</li>
                <li style="margin-bottom: 12px; font-weight: 500;">✓ Connecting Communities</li>
            </ul>
        </div>
    </div>
</div>

<style>
    .about-hero {
        background: var(--charcoal);
        color: white;
        padding: 100px 20px;
        text-align: center;
    }

    .about-hero h1 {
        font-size: 48px;
        font-weight: 900;
        margin-bottom: 16px;
    }

    .about-hero p {
        font-size: 20px;
        opacity: 0.9;
    }

    .about-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
        max-width: 900px;
        margin: 0 auto;
    }

    .about-text h2 {
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 20px;
        color: var(--charcoal);
    }

    .about-text p {
        font-size: 16px;
        line-height: 1.6;
        color: #555;
        margin-bottom: 16px;
    }

    .about-stat-card {
        background: #eff6ff;
        padding: 40px;
        border-radius: 12px;
    }

    .about-stat-card h3 {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 12px;
        color: var(--blue);
    }

    @media (max-width: 768px) {
        .about-grid {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .about-hero h1 {
            font-size: 32px;
        }
    }
</style>

<?php require_once '../includes/footer.php'; ?>
