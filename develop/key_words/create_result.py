import os

def create_combined_file():
    ignored_extensions = ['.png', '.svg', '.webp', '.jpg', '.ico', '.docx', '.pdf', '.sql', '.log']
    ignored_directories = ['.git', 'node_modules', '.qodo', 'frontend', 'logs']
    ignored_files = ['package-lock.json', '.gitignore', '.browserslistrc', 'result.txt', 'create_result.py']
    output_filename = 'result.txt'

    with open(output_filename, 'w', encoding='utf-8') as result_file:
        for root, dirs, files in os.walk('.', topdown=True):
            dirs[:] = [d for d in dirs if d not in ignored_directories]

            for filename in files:
                if (filename == os.path.basename(__file__) or 
                    filename == output_filename or 
                    filename in ignored_files):
                    continue
                
                _, extension = os.path.splitext(filename)
                if extension.lower() in ignored_extensions:
                    continue

                file_path = os.path.join(root, filename)
                
                try:
                    with open(file_path, 'r', encoding='utf-8', errors='ignore') as current_file:
                        content = current_file.read()
                        
                        result_file.write(f'>>!>FILEPATH: {file_path}\n')
                        result_file.write(content)
                        result_file.write('\n<!<ENDOFFILE!<<\n')
                except Exception as e:
                    print(f"Не удалось прочитать файл {file_path}: {e}")

    print(f"Все файлы были успешно записаны в {output_filename}")

if __name__ == '__main__':
    create_combined_file()
