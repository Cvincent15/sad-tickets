<?php
session_start();
include 'php/database_connect.php';

$sql = "SELECT document_id, license_type, expiry_date, status FROM motorist_documents";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>CTMEU</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/motorist.css"/>
  <link rel="stylesheet" href="css/bootstrap.min.css"/>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.11.14/dist/js/bootstrap-datepicker.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-sm navbar-light" style="background-color: #FFFFFF">
  <div class="container-fluid">
  <a class="navbar-brand" href="#">
  <img src="./images/ctmeusmall.png" class="d-inline-block align-text-top">
  <span style="color: #1D3DD1; font-weight: bold;">CTMEU</span> Motorist Portal
</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="d-flex">
        <ul class="navbar-nav me-2">
          <li class="nav-item">
            <a class="nav-link" href="#">LTO Official Page</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Contact</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Dashboard</a>
          </li>
        </ul>
        <div class="dropdown">
  <a class="btn btn-outline-primary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
    <img src="./images/Icon.png" style="margin-right: 10px;">Jak Roberto
  </a>

  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">Log-out</a></li>
  </ul>
</div>
    </div>
    </div>
  </div>
</nav>
<div class="masthead" style="background-image: url('./images/mainbg.png'); padding-top: 60px; padding-bottom: 60px;">
  <section class="container bg-white w-75 text-dark mx-auto p-2 rounded-5">
    <form id="profileForm" action="#!">
      <div class="row d-flex justify-content-center align-items-center">
        <div class="col-md-auto mb-4">
          <h1 class="reg">
            <img src="./images/alternate_file.png" style="margin-right: 10px;">Documents
          </h1>
        </div>
      </div>
      <ul class="nav nav-pills ms-4">
        <li class="nav-item me-4">
          <a class="nav-link active" aria-current="page" href="#">Licenses</a>
        </li>
        <li class="nav-item me-4">
          <a class="nav-link" href="#">Official Receipts</a>
        </li>
        <li class="nav-item me-4">
          <a class="nav-link" href="#">No Apprehension</a>
        </li>
      </ul>
      <table class="table table-bordered text-center mt-4">
        <thead>
            <tr>
                <th scope="col">Number</th>
                <th scope="col">License Type</th>
                <th scope="col">Expiry Date</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $row['document_id'] . '</td>';
                echo '<td>' . $row['license_type'] . '</td>';
                echo '<td>' . $row['expiry_date'] . '</td>';
                echo '<td>' . $row['status'] . '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>

      <div class="row">
        <div class="col">
          <button type="button" class="btn btn-lg btn-danger ms-4 mt-5" onclick="redirectToMain()">Close</button>
        </div>
      </div>
    </form>
  </section>
</div>
<script>
  function redirectToRegister() {
    window.location.href = 'motoristSignup.php';
  }

  function redirectToLogin() {
    window.location.href = 'motorist_login.php';
  }

  function redirectToMain() {
    window.location.href = 'MotoristMain.php';
  }
</script>
<script>
  $(document).ready(function() {
    $('#datepicker').datepicker();
  });
</script>
<script src="./js/stepper.js"></script>
</body>
</html>