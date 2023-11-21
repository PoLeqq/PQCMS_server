<?php

/**
 * Klasa umożliwiająca operację na obiekcie o podanej nazwie z pliku electrocms/config/config.json
 */
class JSONObject
{
    /**
     * Nazwa pliku
     */
    protected string $dir;
    /**
     * Dane pobrane z pliku JSON
     */
    protected mixed $data;
    /**
     * Nazwa modułu (głównego elementu w JSONie)
     */
    protected string $name;

    /**
     * Pobiera plik electrocms/config/$name.json i zwraca obiekt o podanej nazwie
     * $name - nazwa modułu (głównego elementu w JSONie)
     * $dir - nazwa pliku
     */
    function __construct(string $name, string $dir)
    {
        $data = file_get_contents(__DIR__ . $dir);
        $decoded_data = json_decode($data, true);
        $this->data = $decoded_data[$name];
        $this->dir = __DIR__.$dir;
        $this->name = $name;
    }

    /**
     * Zwraca obiekt (dziecko) z aktualnego obiektu (rodzica)
     */
    protected function getObject($name)
    {
        return $this->data[$name];
    }

    /**
     * Zwraca nazwę pliku, na którym działa klasa
     */
    public function getDir(): string
    {
        return $this->dir;
    }

    /**
     * Zwraca wszystkie informacje o obiekcie w formie JSON
     * @return object dane w JSON
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Zmienia dane w obiekcie
     * @param string $key klucz
     * @param mixed $value wartość
     */
    public function setObject(string $key, mixed $value): void
    {
        // TODO Save do logów?
        $this->data[$key] = $value;
    }

    /**
     * Zapisuje dane aktualnie znajdujące się w $data.js
     */
    public function saveData(): void
    {
        $data = json_decode(file_get_contents($this->dir),true);
        // $config = array_replace_recursive($this->config,$config[$this->name]);
        $data[$this->name] = $this->data;

        file_put_contents($this->dir,json_encode($data,JSON_PRETTY_PRINT));
    }
}