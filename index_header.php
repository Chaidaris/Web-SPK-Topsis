<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>SISTEM PENDUKUNG KEPUTUSAN</title>
   <link rel="stylesheet" href="css/bootstrap.css">
   <style>
      li:hover {
         background-color: #f5f5f5;
      }

      .tableFixHead {
         overflow: auto;
         height: 600px;
      }

      .tableFixHead thead th {
         position: sticky;
         top: 0;
         z-index: 1;
      }

      .table thead tr th {
         background-color: white;
         border-collapse: collapse;
         padding-top: 15px;
      }

      .padding {
         padding-top: 0;
      }
   </style>
</head>

<body style="background-color: #ebecf8;">
   <script src="js/bootstrap.js"></script>
   <script src="js/jquery.js"></script>
   <div id="app">
      <nav class="navbar navbar-expand-lg fixed-top" style="background-color: white;">
         <div class="container-fluid">
            <h1 class="navbar-brand" href="index.php">Perhitungan TOPSIS</h1>
         </div>
      </nav>