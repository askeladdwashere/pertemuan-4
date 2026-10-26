<?php declare(strict_types=1);

/* 
====================================================================
  PRAKTIKUM PBO: SIKLUS HIDUP OBJEK & OVERLOADING (5 BAGIAN)
====================================================================
*/

// ==========================================
// DEKLARASI KELAS
// ==========================================

class Mahasiswa {
    private string $nim;
    private string $nama;
    private int $semester;
    private string $fakultas;
    private array $data = [];

    public function __construct(string $nim, string $nama, int $semester, string $fakultas = "Belum diisi") {
        $this->nim = $nim;
        $this->nama = $nama;
        $this->semester = $semester;
        $this->fakultas = $fakultas;
    }

    public function getInfo(): string {
        return "{$this->nim} - {$this->nama} (Semester {$this->semester}) | Fakultas: {$this->fakultas}";
    }

    // Overloading: __set (Dengan Whitelist Atribut)
    public function __set(string $name, mixed $value): void {
        $diizinkan = ['hobi', 'novelFavorit'];
        if (!in_array($name, $diizinkan, true)) {
            throw new InvalidArgumentException("Atribut '$name' tidak diizinkan.");
        }
        $this->data[$name] = $value;
        echo "[__set] Atribut '$name' diisi.<br>\n";
    }

    // Overloading: __get
    public function __get(string $name): mixed {
        if (array_key_exists($name, $this->data)) {
            echo "[__get] Membaca atribut '$name'.<br>\n";
            return $this->data[$name];
        }
        echo "[__get] Atribut '$name' tidak ditemukan.<br>\n";
        return null;
    }

    // Overloading: __isset
    public function __isset(string $name): bool {
        echo "[__isset] Memeriksa atribut '$name'.<br>\n";
        return isset($this->data[$name]);
    }

    // Overloading: __unset
    public function __unset(string $name): void {
        echo "[__unset] Menghapus atribut '$name'.<br>\n";
        unset($this->data[$name]);
    }

    // Overloading: __call (Method Overloading)
    public function __call(string $name, array $arguments): mixed {
        // Method diizinkan (tanpa argumen)
        if ($name === 'cetakRingkas' && count($arguments) === 0) {
            echo "Mahasiswa: {$this->nama}<br>\n";
            return null;
        }

        // Method diizinkan (1 argumen)
        if ($name === 'cetakLengkap' && count($arguments) === 1) {
            echo "Mahasiswa: {$this->nama} | Novel Favorit: {$arguments[0]}<br>\n";
            return null;
        }

        // Method tidak dikenal atau argumen salah
        throw new BadMethodCallException("Method '$name' tidak tersedia atau argumen tidak sesuai.");
    }
}


class Buku {
    private string $judul;
    private string $penulis;
    private int $tahun;

    public function __construct(string $judul, string $penulis, int $tahun) {
        $this->judul = $judul;
        $this->penulis = $penulis;
        $this->tahun = $tahun;
        echo "[Constructor] Buku '{$this->judul}' dibuat<br>\n";
    }

    public function getInfoBuku(): string {
        return "Judul: {$this->judul} | Penulis: {$this->penulis} | Tahun: {$this->tahun}";
    }

    public function __destruct() {
        echo "[Destructor] Buku '{$this->judul}' dihancurkan<br>\n";
    }
}


// ==========================================
// BAGIAN EKSEKUSI / OUTPUT PENGUJIAN (5 BAGIAN)
// ==========================================

// --- BAGIAN 1: Constructor Mahasiswa ---
echo "<h3>--- BAGIAN 1: Constructor Mahasiswa ---</h3>\n";
$mhs1 = new Mahasiswa("2525252525", "Yhwach", 3);
$mhs2 = new Mahasiswa("2530521061", "Aizen Sosuke", 14, "Sains dan Teknologi");

echo "Objek 1 (tanpa argumen fakultas): " . $mhs1->getInfo() . "<br>\n";
echo "Objek 2 (dengan argumen fakultas): " . $mhs2->getInfo() . "<br>\n";
echo "<br><hr><br>\n";


// --- BAGIAN 2: Property Overloading (__set, __get, __isset, __unset) ---
echo "<h3>--- BAGIAN 2: Property Overloading ---</h3>\n";
echo "<b>1. Uji __set()</b><br>\n";
$mhs1->hobi = "Membaca Light Novel";
$mhs1->novelFavorit = "Overlord";
echo "<br>\n";

echo "<b>2. Uji __get()</b><br>\n";
echo "Hobi: " . $mhs1->hobi . "<br>\n";
echo "Novel Favorit: " . $mhs1->novelFavorit . "<br>\n";
echo "<br>\n";

echo "<b>3. Uji __isset() dan __unset()</b><br>\n";
var_dump(isset($mhs1->novelFavorit));
echo "<br>\n";
unset($mhs1->novelFavorit);
echo "<br><hr><br>\n";


// --- BAGIAN 3: Exception Handling Property Overloading (Atribut Terlarang) ---
echo "<h3>--- BAGIAN 3: Exception Atribut Terlarang (__set) ---</h3>\n";
try {
    echo "<b>Mencoba mengisi atribut terlarang 'alamat':</b><br>\n";
    $mhs1->alamat = "Hueco Mundo";
} catch (InvalidArgumentException $e) {
    echo "Exception Ditangkap: " . htmlspecialchars($e->getMessage()) . "<br>\n";
}
echo "<br><hr><br>\n";


// --- BAGIAN 4: Method Overloading (__call) ---
echo "<h3>--- BAGIAN 4: Method Overloading (__call) ---</h3>\n";
echo "<b>1. Method yang diizinkan (tanpa argumen)</b><br>\n";
try {
    $mhs1->cetakRingkas();
} catch (BadMethodCallException $e) {
    echo "Exception: " . htmlspecialchars($e->getMessage()) . "<br>\n";
}
echo "<br>\n";

echo "<b>2. Method dengan 1 argumen</b><br>\n";
try {
    $mhs1->cetakLengkap("Bleach: Can't Fear Your Own World");
} catch (BadMethodCallException $e) {
    echo "Exception: " . htmlspecialchars($e->getMessage()) . "<br>\n";
}
echo "<br>\n";

echo "<b>3. Method tidak dikenal (menghasilkan Exception)</b><br>\n";
try {
    $mhs1->cetakLengkapBaru();
} catch (BadMethodCallException $e) {
    echo "Exception: " . htmlspecialchars($e->getMessage()) . "<br>\n";
}
echo "<br>\n";

echo "<b>4. Method diizinkan tapi argumen salah</b><br>\n";
try {
    $mhs1->cetakRingkas("Argumen Tambahan Salah");
} catch (BadMethodCallException $e) {
    echo "Exception: " . htmlspecialchars($e->getMessage()) . "<br>\n";
}
echo "<br><hr><br>\n";


// --- BAGIAN 5: Skenario Destructor Buku (A, B, C) ---
echo "<h3>--- BAGIAN 5: Destructor Buku ---</h3>\n";
echo "<b>Skenario A: unset tanpa alias</b><br>\n";
$bukuA = new Buku("Sword Art Online", "Reki Kawahara", 2009);
unset($bukuA);
echo "Setelah unset(\$bukuA) -> destructor langsung berjalan<br>\n";
echo "<br>\n";

echo "<b>Skenario B: unset dengan alias</b><br>\n";
$bukuB = new Buku("Re:Zero", "Tappei Nagatsuki", 2014);
$alias = $bukuB;
unset($bukuB);
echo "Setelah unset(\$bukuB) -> destructor BELUM berjalan, objek masih bisa diakses lewat alias:<br>\n";
echo $alias->getInfoBuku() . "<br>\n";
unset($alias);
echo "Setelah unset(\$alias) -> destructor berjalan<br>\n";
echo "<br>\n";

echo "<b>Skenario C: tanpa unset</b><br>\n";
$bukuC = new Buku("Overlord", "Kugane Maruyama", 2012);
echo "Akhir skrip... destructor Overlord akan berjalan secara otomatis di bawah ini.<br>\n";