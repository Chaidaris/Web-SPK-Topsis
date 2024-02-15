<?php
include 'koneksi.php';

// Mulai session
session_start();
// Jika session ada maka include inc_header.php dan jika tidak maka include index_header.php
if (isset($_SESSION['username'])) {
    include 'index_header.php';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Include Bootstrap CSS link here -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Hasil Perhitungan TOPSIS</title>
</head>

<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                Tabel Hasil Perhitungan TOPSIS
            </div>
            <div class="card-body table-responsive">
                <?php
                // Ambil data alternatif dari database
                $query = "SELECT * FROM spk";
                $result = $koneksi->query($query);

                if (!$result) {
                    die("Query error: " . $koneksi->error);
                }

                // Inisialisasi matriks keputusan
                $matrix = [];
                $idMatrix = [];
                $nameMatrix = [];

                while ($row = $result->fetch_assoc()) {
                    $idMatrix[] = $row['alternative_id'];
                    $nameMatrix[] = $row['alternative_name'];
                    $matrix[] = [
                        'C1' => floatval($row['c1']),
                        'C2' => floatval($row['c2']),
                        'C3' => floatval($row['c3']),
                        'C4' => floatval($row['c4']),
                        'C5' => floatval($row['c5']),
                        'C6' => floatval($row['c6']),
                        'C7' => floatval($row['c7']),
                        'C8' => floatval($row['c8']),
                    ];
                }

                // Bobot Manual
                $bobot = [0.1772, 0.1255, 0.2202, 0.0930, 0.0844, 0.0870, 0.0870, 0.1255];

                // Fungsi untuk transpose array
                function array_transpose($array)
                {
                    return array_map(null, ...$array);
                }

                // Tahap pertama: Mengkuadratkan masing-masing nilai matriks
                $squaredMatrix = array_map(function ($row) {
                    return array_map(function ($val) {
                        return pow($val, 2);
                    }, $row);
                }, $matrix);

                // Tahap kedua: Mencari akar dari total nilai kuadrat setiap kriteria
                $sumSquares = array_map(function ($col) {
                    return sqrt(array_sum($col));
                }, array_transpose($squaredMatrix));

                // Tahap ketiga: Membagi setiap elemen matriks dengan hasil di atas
                $normalizedMatrix = array_map(function ($row) use ($sumSquares) {
                    return array_map(function ($val, $sum) {
                        return $val / $sum;
                    }, $row, $sumSquares);
                }, $matrix);

                // Tahap keempat: Membagi masing-masing bobot kriteria dengan total bobot kriteria
                $totalBobot = array_sum($bobot);
                $normalizedBobot = array_map(function ($weight) use ($totalBobot) {
                    return $weight / $totalBobot;
                }, $bobot);

                // Tahap kelima: Mengalikan matriks normalisasi dengan bobot normal di atas
                $weightedMatrix = array_map(function ($row) use ($normalizedBobot) {
                    return array_map(function ($val, $weight) {
                        return $val * $weight;
                    }, $row, $normalizedBobot);
                }, $normalizedMatrix);

                // Tahap keenam: Perhitungan solusi ideal sesuai dengan atribut masing-masing kriteria
                $idealPositive = array_map('max', ...$weightedMatrix);
                $idealNegative = array_map('min', ...$weightedMatrix);

                // Tahap ketujuh: Menghitung jarak solusi ideal dengan mengkuadratkan selisih matriks normalisasi terbobot dengan solusi ideal positif dan negatif
                $positiveDistances = array_map(function ($row) use ($idealPositive) {
                    return sqrt(array_sum(array_map(function ($val, $ideal) {
                        return pow($ideal - $val, 2);  // Perubahan pada bagian ini
                    }, $row, $idealPositive)));
                }, $weightedMatrix);

                $negativeDistances = array_map(function ($row) use ($idealNegative) {
                    return sqrt(array_sum(array_map(function ($val, $ideal) {
                        return pow($val - $ideal, 2);
                    }, $row, $idealNegative)));
                }, $weightedMatrix);

                // Tahap kedelapan: Menghitung total nilai jarak solusi ideal (positif dan negatif)
                $totalPositiveDistance = $positiveDistances;
                $totalNegativeDistance = $negativeDistances;

                // Tahap kesembilan: Perhitungan nilai preferensi berdasarkan jarak solusi ideal positif dan negatif
                $vPreferencesMatrix = array_map(function ($totalPosDistance, $totalNegDistance) {
                    return $totalNegDistance / ($totalNegDistance + $totalPosDistance);
                }, $totalPositiveDistance, $totalNegativeDistance);


                // Tentukan peringkat
                arsort($vPreferencesMatrix);
                $rankings = array_keys($vPreferencesMatrix);
                ?>

                <!-- Tabel Matriks Keputusan -->
                <h4>Matriks Keputusan (R)</h4>
                <table class="table table-bordered">
                    <!-- Header -->
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Alternative ID</th>
                            <th scope="col">Alternative Name</th>
                            <?php
                            foreach ($matrix[0] as $key => $value) {
                                echo "<th scope='col'>$key</th>";
                            }
                            ?>
                        </tr>
                    </thead>
                    <!-- Data -->
                    <tbody>
                        <?php
                        foreach ($matrix as $index => $row) {
                            echo "<tr>";
                            echo "<td>A{$idMatrix[$index]}</td>";
                            echo "<td>{$nameMatrix[$index]}</td>";
                            foreach ($row as $value) {
                                echo "<td>$value</td>";
                            }
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Tabel Matriks Normalisasi -->
                <h4>Matriks Normalisasi (V)</h4>
                <table class="table table-bordered">
                    <!-- Header -->
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Alternative ID</th>
                            <th scope="col">Alternative Name</th>
                            <?php
                            foreach ($normalizedMatrix[0] as $key => $value) {
                                $criteriaNumber = $key + 1;
                                echo "<th scope='col'>$criteriaNumber</th>";
                            }
                            ?>
                        </tr>
                    </thead>
                    <!-- Data -->
                    <tbody>
                        <?php
                        foreach ($normalizedMatrix as $index => $row) {
                            echo "<tr>";
                            echo "<td>A{$idMatrix[$index]}</td>";
                            echo "<td>{$nameMatrix[$index]}</td>";
                            foreach ($row as $value) {
                                echo "<td>$value</td>";
                            }
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Tabel Matriks Solusi Ideal Positif (A+) -->
                <h4>Matriks Solusi Ideal Positif (A+)</h4>
                <table class="table table-bordered">
                    <!-- Header -->
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Criteria</th>
                            <th scope="col">Value</th>
                        </tr>
                    </thead>
                    <!-- Data -->
                    <tbody>
                        <?php
                        foreach ($idealPositive as $key => $value) {
                            $criteriaNumber = $key + 1;
                            echo "<tr>";
                            echo "<td>C$criteriaNumber</td>";
                            echo "<td>$value</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Tabel Matriks Solusi Ideal Negatif (A-) -->
                <h4>Matriks Solusi Ideal Negatif (A-)</h4>
                <table class="table table-bordered">
                    <!-- Header -->
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Criteria</th>
                            <th scope="col">Value</th>
                        </tr>
                    </thead>
                    <!-- Data -->
                    <tbody>
                        <?php
                        foreach ($idealNegative as $key => $value) {
                            $criteriaNumber = $key + 1;
                            echo "<tr>";
                            echo "<td>C$criteriaNumber</td>";
                            echo "<td>$value</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Tabel Jarak Relatif Positif -->
                <h4>Jarak Relatif Positif (D+)</h4>
                <table class="table table-bordered">
                    <!-- Header -->
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Alternative ID</th>
                            <th scope="col">Alternative Name</th>
                            <th scope="col">Value</th>
                        </tr>
                    </thead>
                    <!-- Data -->
                    <tbody>
                        <?php
                        foreach ($positiveDistances as $index => $value) {
                            echo "<tr>";
                            echo "<td>A{$idMatrix[$index]}</td>";
                            echo "<td>{$nameMatrix[$index]}</td>";
                            echo "<td>$value</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Tabel Jarak Relatif Negatif -->
                <h4>Jarak Relatif Negatif (D-)</h4>
                <table class="table table-bordered">
                    <!-- Header -->
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Alternative ID</th>
                            <th scope="col">Alternative Name</th>
                            <th scope="col">Value</th>
                        </tr>
                    </thead>
                    <!-- Data -->
                    <tbody>
                        <?php
                        foreach ($negativeDistances as $index => $value) {
                            echo "<tr>";
                            echo "<td>A{$idMatrix[$index]}</td>";
                            echo "<td>{$nameMatrix[$index]}</td>";
                            echo "<td>$value</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Tabel Nilai v Preference -->
                <h4>Nilai V Preference</h4>
                <table class="table table-bordered">
                    <!-- Header -->
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Alternative ID</th>
                            <th scope="col">Alternative Name</th>
                            <th scope="col">Value</th>
                        </tr>
                    </thead>
                    <!-- Data -->
                    <tbody>
                        <?php
                        foreach ($vPreferencesMatrix as $index => $value) {
                            echo "<tr>";
                            echo "<td>A{$idMatrix[$index]}</td>";
                            echo "<td>{$nameMatrix[$index]}</td>";
                            echo "<td>$value</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Tabel Hasil Perhitungan -->
                <h4>Hasil Perangkingan</h4>
                <table class="table table-bordered">
                    <!-- Header -->
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Rank</th>
                            <th scope="col">Alternative ID</th>
                            <th scope="col">Alternative Name</th>
                            <th scope="col">Nilai v Preference</th>
                        </tr>
                    </thead>
                    <!-- Data -->
                    <tbody>
                        <?php
                        foreach ($rankings as $index => $rank) {
                            // Tambahkan 1 karena peringkat dimulai dari 1
                            $currentRank = $index + 1;
                            echo "<tr>
                                    <td>{$currentRank}</td>
                                    <td>A{$idMatrix[$rank]}</td>
                                    <td>{$nameMatrix[$rank]}</td>
                                    <td>{$vPreferencesMatrix[$rank]}</td>
                                </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
<?php
include "inc_footer.php";
?>
