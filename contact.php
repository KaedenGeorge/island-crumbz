<?php include 'header.php'; ?>

<h1>Contact Us</h1>

<div class="two-column">
    <div>
        <h2>Get in Touch</h2>
        <p>Have a question or want to place a custom order? Send us a message.</p>
        <ul class="styled-list">
            <li>Email: islandcrumbz@gmail.com</li>
            <li>WhatsApp: +1 (473) 536-2071</li>
            <li>Instagram: @islandcrumbzgnd</li>
        </ul>
    </div>

   <div class="contact-form-container">
    <h2>Contact Form</h2>

    <form method="POST" action="" class="contact-form">
        <label>Name</label>
        <input type="text" name="name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Message</label>
        <textarea name="message" required></textarea>

        <input type="submit" name="submit" value="Send" class="btn btn-primary">
    </form>
</div>

</div>

<?php include 'footer.php'; ?>
