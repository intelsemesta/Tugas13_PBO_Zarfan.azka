<?php
// toko.php - Aplikasi CLI Toko Retail
// Jalankan lewat cmd: php toko.php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "toko_retail";

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error() . "\n");
}

function bacaInput($label) {
    echo $label;
    return trim(fgets(STDIN));
}

function tampilkanMenu() {
    echo "\n";
    echo "+----------------------------+\n";
    echo "|      MENU TOKO RETAIL      |\n";
    echo "+----------------------------+\n";
    echo "  1. Tampil Semua Data\n";
    echo "  2. Tambah Data\n";
    echo "  3. Cari Data\n";
    echo "  4. Ubah Data\n";
    echo "  5. Hapus Data\n";
    echo "  0. Keluar\n";
    echo "+----------------------------+\n";
}

function tampilSemuaData($conn) {
    $sql = "SELECT * FROM barang ORDER BY id";
    $result = mysqli_query($conn, $sql);

    echo "\n";
    echo "==================================================================\n";
    echo "                     DAFTAR BARANG TOKO RETAIL\n";
    echo "==================================================================\n";
    printf("%-4s %-8s %-20s %-10s %-6s\n", "#", "Kode", "Nama Barang", "Harga", "Stok");
    echo "------------------------------------------------------------------\n";

    $no = 1;
    $total = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        printf("%-4s %-8s %-20s %-10s %-6s\n", $no, $row['kode'], $row['nama_barang'], $row['harga'], $row['stok']);
        $no++;
        $total++;
    }
    echo "==================================================================\n";
    echo "Total: $total barang\n";
}

function tambahData($conn) {
    echo "\n--- TAMBAH DATA BARANG ---\n";
    $kode = bacaInput("Kode Barang  : ");
    $nama = bacaInput("Nama Barang  : ");
    $harga = bacaInput("Harga        : ");
    $stok = bacaInput("Stok         : ");

    $kode = mysqli_real_escape_string($conn, $kode);
    $nama = mysqli_real_escape_string($conn, $nama);
    $harga = (int) $harga;
    $stok = (int) $stok;

    $sql = "INSERT INTO barang (kode, nama_barang, harga, stok) VALUES ('$kode', '$nama', $harga, $stok)";

    if (mysqli_query($conn, $sql)) {
        echo "\nData berhasil ditambahkan.\n";
    } else {
        echo "\nGagal menambah data: " . mysqli_error($conn) . "\n";
    }
}

function cariData($conn) {
    echo "\n--- CARI DATA BARANG ---\n";
    $keyword = bacaInput("Masukkan kode/nama barang : ");
    $keyword = mysqli_real_escape_string($conn, $keyword);

    $sql = "SELECT * FROM barang WHERE kode LIKE '%$keyword%' OR nama_barang LIKE '%$keyword%'";
    $result = mysqli_query($conn, $sql);

    echo "\n------------------------------------------------------------------\n";
    printf("%-4s %-8s %-20s %-10s %-6s\n", "#", "Kode", "Nama Barang", "Harga", "Stok");
    echo "------------------------------------------------------------------\n";

    $no = 1;
    $ditemukan = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        printf("%-4s %-8s %-20s %-10s %-6s\n", $no, $row['kode'], $row['nama_barang'], $row['harga'], $row['stok']);
        $no++;
        $ditemukan++;
    }
    echo "------------------------------------------------------------------\n";

    if ($ditemukan == 0) {
        echo "Data tidak ditemukan.\n";
    } else {
        echo "Ditemukan $ditemukan data.\n";
    }
}

function ubahData($conn) {
    echo "\n--- UBAH DATA BARANG ---\n";
    $kode = bacaInput("Masukkan kode barang yang akan diubah : ");
    $kodeEsc = mysqli_real_escape_string($conn, $kode);

    $cek = mysqli_query($conn, "SELECT * FROM barang WHERE kode = '$kodeEsc'");
    if (mysqli_num_rows($cek) == 0) {
        echo "Data dengan kode tersebut tidak ditemukan.\n";
        return;
    }

    $row = mysqli_fetch_assoc($cek);
    echo "Data lama: {$row['kode']} | {$row['nama_barang']} | {$row['harga']} | {$row['stok']}\n";

    $nama = bacaInput("Nama Barang Baru (kosongkan jika tidak berubah) : ");
    $harga = bacaInput("Harga Baru (kosongkan jika tidak berubah)       : ");
    $stok = bacaInput("Stok Baru (kosongkan jika tidak berubah)        : ");

    $nama = ($nama === "") ? $row['nama_barang'] : mysqli_real_escape_string($conn, $nama);
    $harga = ($harga === "") ? $row['harga'] : (int) $harga;
    $stok = ($stok === "") ? $row['stok'] : (int) $stok;

    $sql = "UPDATE barang SET nama_barang='$nama', harga=$harga, stok=$stok WHERE kode='$kodeEsc'";

    if (mysqli_query($conn, $sql)) {
        echo "\nData berhasil diubah.\n";
    } else {
        echo "\nGagal mengubah data: " . mysqli_error($conn) . "\n";
    }
}

function hapusData($conn) {
    echo "\n--- HAPUS DATA BARANG ---\n";
    $kode = bacaInput("Masukkan kode barang yang akan dihapus : ");
    $kodeEsc = mysqli_real_escape_string($conn, $kode);

    $cek = mysqli_query($conn, "SELECT * FROM barang WHERE kode = '$kodeEsc'");
    if (mysqli_num_rows($cek) == 0) {
        echo "Data dengan kode tersebut tidak ditemukan.\n";
        return;
    }

    $konfirmasi = bacaInput("Yakin ingin menghapus data ini? (y/n) : ");
    if (strtolower($konfirmasi) === "y") {
        $sql = "DELETE FROM barang WHERE kode = '$kodeEsc'";
        if (mysqli_query($conn, $sql)) {
            echo "\nData berhasil dihapus.\n";
        } else {
            echo "\nGagal menghapus data: " . mysqli_error($conn) . "\n";
        }
    } else {
        echo "\nPenghapusan dibatalkan.\n";
    }
}

// ================= PROGRAM UTAMA =================
$jalan = true;
while ($jalan) {
    tampilkanMenu();
    $pilihan = bacaInput("Pilihan : ");

    switch ($pilihan) {
        case "1":
            tampilSemuaData($conn);
            break;
        case "2":
            tambahData($conn);
            break;
        case "3":
            cariData($conn);
            break;
        case "4":
            ubahData($conn);
            break;
        case "5":
            hapusData($conn);
            break;
        case "0":
            echo "\nTerima kasih. Program selesai.\n";
            $jalan = false;
            break;
        default:
            echo "\nPilihan tidak valid, silakan coba lagi.\n";
    }
}

mysqli_close($conn);