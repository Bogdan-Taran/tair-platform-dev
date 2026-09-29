import re

# Имя вашего исходного файла и имя файла, куда сохранить результат
INPUT_FILE = "summer-camp-2026.blade.php"
OUTPUT_FILE = "summer-camp-2026_processed.blade.php"

# Регулярное выражение для поиска тегов img с относительным путем
# Оно захватывает всё, что идет после '../' внутри атрибута src
pattern = r'<img src="\.\./(src/img/[^"]+)"'


def convert_img_paths(input_path, output_path):
    try:
        with open(input_path, "r", encoding="utf-8") as infile, open(
            output_path, "w", encoding="utf-8"
        ) as outfile:

            for line in infile:
                # Заменяем src="../src/img/..." на src="{{ url('frontend/src/img/...') }}"
                processed_line = re.sub(
                    pattern, r'<img src="{{ url(\'frontend/\1\') }}"', line
                )
                outfile.write(processed_line)

        print(
            f"🎉 Обработка завершена! Результат сохранен в файл: {output_path}"
        )

    except FileNotFoundError:
        print(f"❌ Ошибка: Файл '{input_path}' не найден.")


# Запуск скрипта
if __name__ == "__main__":
    convert_img_paths(INPUT_FILE, OUTPUT_FILE)
