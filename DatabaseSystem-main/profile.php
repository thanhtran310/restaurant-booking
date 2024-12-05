<?php
include "layout/header.php";
if (!isset($_SESSION['email'])) {
    header("Location: loginpage.php");
    exit;
}
?>

<div class="container profile-container py-5">
    <div class="row">
        <div class="col-lg-6 mx-auto border shadow p-4">
            <h2 class="mb-4">Profile</h2>
            <hr />
            
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th scope="row">First Name:</th>
                        <td><?= htmlspecialchars($_SESSION['first_name']) ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Last Name:</th>
                        <td><?= htmlspecialchars($_SESSION['last_name']) ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Email:</th>
                        <td><?= htmlspecialchars($_SESSION['email']) ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Phone:</th>
                        <td><?= htmlspecialchars($_SESSION['phone']) ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Address:</th>
                        <td><?= htmlspecialchars($_SESSION['address']) ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Registered at:</th>
                        <td><?= htmlspecialchars($_SESSION['created_at']) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
include "layout/footer.php";
?>
