<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
   <title>Web Sistem Pendukung Keputusan</title>
</head>

<body>
   <?php
   // Mulai session
   session_start();

   // Jika session ada maka include inc_header.php dan jika tidak maka include index_header.php
   if (isset($_SESSION['username'])) {
      include 'index_header.php';
   }
   ?>

   <div class="container-fluid">
      <div class="row justify-content-center mt-5">
            <div class="col-md-8">
               <div class="card" style="border: 0;">
                  <div class="card-body text-center">
                     <h2>Selamat Datang di Web Sistem Pendukung Keputusan</h2>
                     <p>Selamat datang di Portal SPK, portal yang didedikasikan untuk membahas dan memberikan pemahaman mendalam tentang Sistem Pendukung Keputusan (SPK) menggunakan salah satu metode yang paling efektif, yaitu TOPSIS (Technique for Order of Preference by Similarity to Ideal Solution). Kami mengundang Anda untuk menjelajahi dan memahami bagaimana TOPSIS dapat menjadi alat yang sangat berguna dalam pengambilan keputusan dengan menganalisis dan memilah alternatif berdasarkan preferensi dan kesamaan dengan solusi ideal. Mari kita bersama-sama mendalami keunggulan dan penerapan praktis metode TOPSIS untuk meningkatkan efisiensi pengambilan keputusan dalam berbagai konteks.</p>
                  </div>
                  <div class="text-center mb-4">
                     <a href="calculating.php" class="btn btn-primary">Hitung TOPSIS</a>
                  </div>
               </div>
         </div>
      </div>
   </div>


      <div class="row justify-content-center mt-5">
            <div class="col-md-10">
               <div class="card">
                  <div class="card-header text-white bg-primary">
                     Tabel Alternatif dan Kriteria
                  </div>
                  <div class="card-body table-responsive">
                        <table class="table">
                           <colgroup>
                              <col span="1" style="width: 20%;">
                              <col span="1" style="width: 20%;">
                              <col span="1" style="width: 8%;">
                              <col span="1" style="width: 8%;">
                              <col span="1" style="width: 8%;">
                              <col span="1" style="width: 8%;">
                              <col span="1" style="width: 8%;">
                              <col span="1" style="width: 8%;">
                              <col span="1" style="width: 8%;">
                              <col span="1" style="width: 8%;">
                           </colgroup>
                           <thead>
                              <tr>
                                    <th scope="col">Altenative_ID</th>
                                    <th scope="col">Altenative</th>
                                    <th scope="col">C1</th>
                                    <th scope="col">C2</th>
                                    <th scope="col">C3</th>
                                    <th scope="col">C4</th>
                                    <th scope="col">C5</th>
                                    <th scope="col">C6</th>
                                    <th scope="col">C7</th>
                                    <th scope="col">C8</th>

                              </tr>
                           </thead>
                           <tbody>
                              <?php
                              include 'koneksi.php';
                                // Query untuk menampilkan data dari tabel pengaduan
                                $sql = mysqli_query($koneksi, "SELECT * FROM spk");
                                 while ($data = mysqli_fetch_array($sql)) {
                                    echo "<tr>";
                                    echo "<td>" . $data['alternative_id'] . "</td>";
                                    echo "<td>" . $data['alternative_name'] . "</td>";
                                    echo "<td>" . $data['c1'] . "</td>";
                                    echo "<td>" . $data['c2'] . "</td>";
                                    echo "<td>" . $data['c3'] . "</td>";
                                    echo "<td>" . $data['c4'] . "</td>";
                                    echo "<td>" . $data['c5'] . "</td>";
                                    echo "<td>" . $data['c6'] . "</td>";
                                    echo "<td>" . $data['c7'] . "</td>";
                                    echo "<td>" . $data['c8'] . "</td>";
                                    echo "</tr>";
                              }
                              ?>
                           </tbody>
                        </table>
                  </div>
               </div>
            </div>
      </div>
   </div>

<?php
include "inc_footer.php";
?>
</body>
</html>