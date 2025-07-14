CREATE DATABASE akademik_pnl;
USE akademik_pnl;

CREATE TABLE jurusan (
  kodejurusan VARCHAR(6) NOT NULL PRIMARY KEY,
  namajurusan VARCHAR(60) NOT NULL,
  kajur VARCHAR(60) NOT NULL,
  nipkajur VARCHAR(18) NOT NULL,
  ket VARCHAR(40) DEFAULT NULL
);

CREATE TABLE kelas (
  idkelas VARCHAR(2) NOT NULL PRIMARY KEY,
  namakelas VARCHAR(20) NOT NULL,
  ket VARCHAR(10) DEFAULT NULL
);

CREATE TABLE prodi (
  kodeprodi VARCHAR(6) NOT NULL PRIMARY KEY,
  namaprodi VARCHAR(50) NOT NULL,
  statusakred VARCHAR(20) NOT NULL,
  jenjang CHAR(2) NOT NULL,
  namakaprodi VARCHAR(18) NOT NULL,
  nipkaprodi VARCHAR(18) NOT NULL,
  ket VARCHAR(40) DEFAULT NULL,
  kodejurusan VARCHAR(6) NOT NULL,
  FOREIGN KEY (kodejurusan) REFERENCES jurusan(kodejurusan) ON UPDATE CASCADE
);

CREATE TABLE mahasiswa (
  nim VARCHAR(13) NOT NULL PRIMARY KEY,
  nama_mhs VARCHAR(40) NOT NULL,
  jkel CHAR(2) NOT NULL,
  alamat VARCHAR(50) NOT NULL,
  tempat VARCHAR(40) NOT NULL,
  tglLahir DATE NOT NULL,
  agama VARCHAR(40) NOT NULL,
  noHp VARCHAR(18) DEFAULT NULL,
  noKK VARCHAR(16) DEFAULT NULL,
  kodeprodi VARCHAR(6) NOT NULL,
  idKelas VARCHAR(2) NOT NULL,
  FOREIGN KEY (kodeprodi) REFERENCES prodi(kodeprodi),
  FOREIGN KEY (idKelas) REFERENCES kelas(idkelas)
);
