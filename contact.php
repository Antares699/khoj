<?php require_once 'includes/header.php'; ?>

<div class="contact-container">
    <div class="contact-header">
        <h1>Contact Us</h1>
        <p>Values, questions, or feedback? We'd love to hear from you.</p>
    </div>

    <div class="contact-grid">
        <div class="contact-form-card">
            <form action="#" method="POST">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Message</label>
                    <textarea class="form-input" rows="5" placeholder="How can we help?" required
                        style="resize: vertical;"></textarea>
                </div>

                <button type="submit" class="cta-btn primary full-width">Send Message</button>
            </form>
        </div>

        <div class="contact-info">
            <div class="info-item">
                <h3>Office</h3>
                <p>Kathmandu, Nepal<br>New Baneshwor</p>
            </div>

            <div class="info-item">
                <h3>Email</h3>
                <p><a href="mailto:support@Khoj.com">support@Khoj.com</a></p>
                <p><a href="mailto:business@Khoj.com">business@Khoj.com</a></p>
            </div>

            <div class="info-item">
                <h3>Business Hours</h3>
                <p>Sunday - Friday<br>9:00 AM - 6:00 PM</p>
            </div>
        </div>
    </div>
</div>

<style>
    .contact-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 100px 20px 60px;
        /* Increased top padding to space from site header */
        min-height: 100vh;
        /* Ensures footer is below the fold */
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
    }

    .contact-header {
        text-align: center;
        margin-bottom: 60px;
    }

    .contact-header h1 {
        font-size: 42px;
        font-weight: 900;
        color: var(--charcoal);
        margin-bottom: 12px;
    }

    .contact-header p {
        font-size: 18px;
        color: #666;
    }

    .contact-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 60px;
        flex-grow: 1;
    }

    .contact-form-card {
        background: #fff;
        padding: 40px;
        border: 1px solid #eee;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        height: fit-content;
    }

    /* Match About Page / .link-cta styling */
    .contact-form-card .cta-btn.primary {
        background: var(--blue);
        color: var(--white);
        border: none;
        padding: 8px 24px;
        border-radius: var(--r);
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-block;
        width: auto;
        /* Not full width to match 'Join Now' look */
        text-align: center;
        text-decoration: none;
    }

    .contact-form-card .cta-btn.primary:hover {
        background: #2b52cc;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(51, 102, 255, 0.2);
    }

    .contact-form-card .cta-btn.primary:active {
        transform: translateY(0);
    }

    .contact-info {
        padding-top: 20px;
    }

    .info-item {
        margin-bottom: 40px;
    }

    .info-item h3 {
        font-size: 18px;
        font-weight: 700;
        color: var(--charcoal);
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-item p {
        color: #555;
        line-height: 1.6;
        font-size: 16px;
    }

    .info-item a {
        color: var(--blue);
        text-decoration: none;
    }

    .info-item a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .contact-grid {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .contact-header h1 {
            font-size: 32px;
        }
    }
</style>

<?php require_once 'includes/footer.php'; ?>
