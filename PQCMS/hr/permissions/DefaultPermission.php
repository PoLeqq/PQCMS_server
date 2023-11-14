<?php 

    const PERMS_FILENAME = __DIR__."/permissions.json";

    class DefaultPermission {
        private $data;
        private $permissionName;
        private $permissionValue;

        public function __construct($permissionName) {
            $this->data = file_get_contents(PERMS_FILENAME);
            $this->permissionName = $permissionName;
            $this->permissionValue = $this->getValue();
        }
        
        public function getName() {
            return $this->permissionName;
        }

        public function getValue() {
            // Odczytaj wartość permisji z pliku JSON na podstawie $this->permissionName
            $decodedData = json_decode($this->data, true);
            $keys = $this->getPermissionDirectory($this->permissionName);
            $value = $decodedData;

            foreach ($keys as $key) {
                if (isset($value[$key])) {
                    $value = $value[$key];
                } else {
                    $value = null;
                    break;
                }
            }

            // Jeżeli nie znaleziono wartości, zwróć domyślną wartość false
            return ($value !== null) ? $value['default'] : false;
        }

        function getPermissionDirectory($permissionName) {
            return explode(".",$permissionName);
        }

        /**
         * Ustawia nową wartość permisji oraz zapisuje dane do pliku.
         *
         * @param  mixed $value
         * @return void
         */
        private function updateKey($lastKey,$value) {
            // Odczytaj dane z pliku JSON
            $decodedData = json_decode($this->data, true);
            $keys = $this->getPermissionDirectory($this->permissionName);
            $target =& $decodedData;
        
            // Przechodzimy przez klucze, aby dotrzeć do odpowiedniej wartości permisji
            foreach ($keys as $key)
                $target =& $target[$key];
        
            // Ustaw nową wartość permisji
            $target[$lastKey] = $value;
        
            // Zapisz zmienione dane do zmiennej $config
            $this->data = json_encode($decodedData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
            // Zapisz dane do pliku
            file_put_contents(PERMS_FILENAME, $this->data);
        }

        public function setValue($newValue) {
            $this->updateKey("default",$newValue);
        }

        public function setDescription($description) {
            $this->updateKey("description",$description);
        }
    }

    function isLetter(string $char) {
        return preg_match('/[a-zA-Z]/', $char);
    }

    function isLowercase(string $char) {
        return preg_match('/[a-z]/', $char);
    }

    function isProperPermission($permissionName) {
        // TODO
        // if(!isLowercase(trim($permissionName[0])))
        //     return false;
        // if(!isLowercase($permissionName[strlen($permissionName)-1]))
        //     return false;

        // $permsDir = getPermissionDirectory($permissionName);

        // foreach($permsDir as $dir) {
        //     $dir = str_replace()
        // }
        
        

        // getPermissionDirectory();

        return false;
        // $dot = -1;
        // for($i = 0; $i < strlen($permissionName); $i++) {
        //     if($permissionName[$i] == '.')
        //         $dot = 
        // }
    }

    // $dp = new DefaultPermission("site.test.lol.*");
    // $dp->setValue(true);
    // $dp->setDescription("ąęćółźż");

    if(isProperPermission("xd"))
        echo "true";
    else
        echo "false";