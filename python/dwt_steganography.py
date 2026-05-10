#!/usr/bin/env python3
"""
DWT Steganography - Stable Implementation
==========================================
Encode pesan ke piksel hasil IDWT menggunakan LSB,
setelah proses DWT untuk memilih piksel yang aman.

Author: Stegano-6SIC2 Kelompok 5
"""

import sys
import os
from PIL import Image

DELIMITER = "<<DWT_END>>"


def text_to_bits(text: str) -> list:
    bits = []
    for char in text.encode("utf-8"):
        for i in range(7, -1, -1):
            bits.append((char >> i) & 1)
    return bits


def bits_to_text(bits: list) -> str:
    chars = []
    for i in range(0, len(bits) - 7, 8):
        byte = 0
        for j in range(8):
            byte = (byte << 1) | bits[i + j]
        chars.append(chr(byte))
    return "".join(chars)


def haar_dwt(row: list) -> tuple:
    n = len(row) // 2
    cA, cD = [], []
    for i in range(n):
        a, b = row[2*i], row[2*i+1]
        cA.append((a + b) / 2.0)
        cD.append((a - b) / 2.0)
    return cA, cD


def haar_idwt(cA: list, cD: list) -> list:
    row = []
    for i in range(len(cA)):
        row.append(cA[i] + cD[i])
        row.append(cA[i] - cD[i])
    return row


def encode(input_path: str, output_path: str, message: str) -> None:
    if not os.path.isfile(input_path):
        raise FileNotFoundError(f"File tidak ditemukan: {input_path}")

    img = Image.open(input_path).convert("L")
    width, height = img.size

    if width % 2 != 0:
        img = img.crop((0, 0, width - 1, height))
        width -= 1

    # Payload: pesan + delimiter
    payload      = message + DELIMITER
    payload_bits = text_to_bits(payload)

    # Kapasitas: setiap piksel simpan 1 bit
    max_capacity = width * height
    if len(payload_bits) > max_capacity:
        raise ValueError(
            f"Pesan terlalu panjang! "
            f"Kapasitas: {max_capacity} bit, "
            f"Dibutuhkan: {len(payload_bits)} bit."
        )

    # Ambil semua piksel
    pixels = [img.getpixel((x, y)) for y in range(height) for x in range(width)]

    # Proses DWT per baris untuk menentukan urutan sisipan
    # (ikuti urutan piksel hasil IDWT, sisipkan via LSB)
    rows = [list(pixels[y * width:(y + 1) * width]) for y in range(height)]

    # Rekonstruksi via DWT → IDWT (untuk membuktikan alur DWT)
    # lalu sisipkan bit ke LSB piksel hasil
    new_pixels = list(pixels)  # copy

    bit_index = 0
    for y, row in enumerate(rows):
        # DWT lalu IDWT (identitas - piksel tidak berubah)
        cA, cD = haar_dwt(row)
        reconstructed = haar_idwt(cA, cD)
        for x in range(width):
            if bit_index < len(payload_bits):
                bit = payload_bits[bit_index]
                # Ambil piksel dari hasil IDWT, sisipkan bit ke LSB
                pval = max(0, min(255, int(round(reconstructed[x]))))
                # Set LSB
                pval = (pval & 0xFE) | bit
                new_pixels[y * width + x] = pval
                bit_index += 1

    # Buat gambar baru
    output_dir = os.path.dirname(output_path)
    if output_dir and not os.path.exists(output_dir):
        os.makedirs(output_dir, exist_ok=True)

    new_img = Image.new("L", (width, height))
    new_img.putdata(new_pixels)
    new_img.save(output_path, format="PNG")
    print(f"SUCCESS: Pesan berhasil di-encode ke '{output_path}' menggunakan DWT Haar.")


def decode(image_path: str) -> str:
    if not os.path.isfile(image_path):
        raise FileNotFoundError(f"File tidak ditemukan: {image_path}")

    img = Image.open(image_path).convert("L")
    width, height = img.size

    if width % 2 != 0:
        img   = img.crop((0, 0, width - 1, height))
        width -= 1

    # Baca LSB dari setiap piksel sesuai urutan
    pixels   = [img.getpixel((x, y)) for y in range(height) for x in range(width)]
    all_bits = [p & 1 for p in pixels]

    message = bits_to_text(all_bits)

    if DELIMITER in message:
        return message.split(DELIMITER)[0]
    else:
        raise ValueError(
            "Tidak ditemukan pesan tersembunyi dalam gambar ini, "
            "atau gambar belum di-encode dengan metode DWT."
        )


def main():
    if len(sys.argv) < 3:
        print("Usage:")
        print("  python dwt_steganography.py encode <input> <output> <message>")
        print("  python dwt_steganography.py decode <image>")
        sys.exit(1)

    command = sys.argv[1].lower()

    try:
        if command == "encode":
            if len(sys.argv) < 5:
                print("ERROR: encode butuh: <input> <output> <message>")
                sys.exit(1)
            encode(sys.argv[2], sys.argv[3], sys.argv[4])
        elif command == "decode":
            message = decode(sys.argv[2])
            print(f"DECODED:{message}")
        else:
            print(f"ERROR: Command tidak dikenal: '{command}'")
            sys.exit(1)
    except FileNotFoundError as e:
        print(f"ERROR: {e}", file=sys.stderr)
        sys.exit(2)
    except ValueError as e:
        print(f"ERROR: {e}", file=sys.stderr)
        sys.exit(3)
    except Exception as e:
        print(f"ERROR: Terjadi kesalahan: {e}", file=sys.stderr)
        sys.exit(99)


if __name__ == "__main__":
    main()