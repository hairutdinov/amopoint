<?php

namespace classes;

use Exception;

class UploadFile {
    private string $target_dir;
    private array $status;

    public function __construct(string $target_dir, bool $create_dir_if_not_exists = true)
    {
        $this->target_dir = $target_dir;

        if ($create_dir_if_not_exists and !$this->target_dir_exists()) {
            $this->create_target_dir();
        }
    }

    public function target_dir_exists(): bool
    {
        return file_exists($this->target_dir);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function create_target_dir()
    {
        if (mkdir($this->target_dir, 0777, true) === false) {
            throw new Exception("Не удалось создать директорию");
        }
    }

    /**
     * @param array $file
     * @return void
     * @throws Exception
     */
    public function upload(array $file): array
    {
        $file_name = basename($file["name"]);
        $target_file = $this->target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (!$file_name) {
            throw new Exception("Необходимо выбрать текстовый файл.");
        }

        if (file_exists($target_file)) {
            throw new Exception("Файл с таким именем уже существует.");
        }

        if ($file_type != "txt") {
            throw new Exception("Допускаются только файлы формата .txt.");
        }

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return [
                "message" => "Файл был успешно загружен.",
                "filename" => htmlspecialchars($file_name),
                "target_file" => $target_file,
            ];
        } else {
            throw new Exception("Извините, произошла ошибка при загрузке файла.");
        }
    }
}
