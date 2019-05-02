<!DOCTYPE html>
<html>
    <head>
        <title>Submission</title>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="css/main.css" />
        <link rel="stylesheet" href="css/bootstrap.min.css" />
    </head>
    <body>
        <h1>Daftar</h1>
        <form method="post" action="index.php" enctype="multipart/form-data">
            <div class="form-group">
                <div class="col-md-2">
                    Nama
                </div>
                <div class="col-md-10">
                    <input name="nama" type="text" class="form-control form-control-sm">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-2">
                    Alamat
                </div>
                <div class="col-md-10">
                    <input name="alamat" type="text" class="form-control form-control-sm">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-2">
                    &nbsp;
                </div>
                <div class="col-md-10">
                    <button class="btn btn-sm btn-success" name="simpan" type="submit">
                        Simpan
                    </button>
                    <button class="btn btn-sm btn-primary" name="loaddata" type="submit">
                        Lihat Data
                    </button>
                </div>
            </div>
        </form>

        <!-- TABLE GOES HERE -->
        <?php
            $host = "dicodingsubmission.database.windows.net";
            // $host = "localhost:3306";
            $user = "tejadicoding899";
            // $user = "root";
            $pass = "T3j4D1c0d1ng";
            // $pass = "";
            $db = "dicodingsubmission";

            // Connecting to DB
            try {
                $conn = new PDO("sqlsrv:server = $host; Database = $db", $user, $pass);
                // $conn = new PDO("mysql:host=$host;dbname=dicodingsubmission", $user, $pass);
                $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            } catch(Exception $e) {
                echo "Failed: " . $e;
            }

            // SAVE
            if (isset($_POST['simpan'])) {
                try {
                    $id = time();
                    $nama = $_POST['nama'];
                    $alamat = $_POST['alamat'];

                    // Insert
                    $sql_insert = "INSERT INTO pengguna (id, nama, alamat) VALUES (?,?,?)";

                    $stmt = $conn->prepare($sql_insert);
                    $stmt->bindValue(1, $id);
                    $stmt->bindValue(2, $nama);
                    $stmt->bindValue(3, $alamat);

                    $stmt->execute();
                } catch (Exception $e) {
                    echo "Failed". $e;
                }

                echo "<h3>Thansk For Your Registration</h3>";
            } else if(isset($_POST['loaddata'])) {
                try {
                    // GET DATA
                    $sql_select = "SELECT * FROM pengguna";

                    $stmt = $conn->query($sql_select);
                    $data = $stmt->fetchAll(); 

                    if (count($data) > 0) {
                        echo "<h2>Daftar Pengguna:</h2>";

                        echo "<table class='table'>";
                            echo "<thead>";
                                echo "<tr>";
                                    echo "<th>Nama</th>";
                                    echo "<th>Alamat</th>";
                                echo "</tr>";
                            echo "</thead>";

                            echo "<tbody>";
                            foreach($data as $pengguna) {
                                echo "<tr>";
                                    echo "<td>" .$pengguna["nama"]. "</td>";
                                    echo "<td>" .$pengguna["alamat"]. "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody>";

                        echo "</table>";
                    } else {
                        echo "<h3>No Data Found</h3>";    
                    }
                } catch (Exception $e) {
                    echo "Failed". $e;
                }
            }

            // Lihat
        ?>

    </body>
</html>	
	