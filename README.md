# ElectroCMS

# MODUŁY (Funkcjonalności)

## Logowanie
- [ ] Sesja użytkownika*(min.)
- [ ] Logi logowań
- [ ] Zapamietanie loginu/hasła*(on/off)

## Dane
- [ ] Podstawowe dane
   - [x] przechowywane w pliku json (`electrocms/data/data.json`)
   - [x] APIk do pobierania oraz edycji odpowiednich danych
   - [ ] Zabezpieczenie przed nieautoryzowanym dostępem

## Panel
- [ ] Logi
   - [ ] Logowania
   - [ ] Zmiana tekstu (historia)
   - [ ] Użytkowników
- [ ] Wizyty strony
- [ ] Aktualizacja `data.json` (Dane - Podstawowe dane)
- [ ] Umożliwienie zarządzania HR z poziomu panelu
- [ ] Wejście na witrynę internetową od strony edytora 

## HR (Human Resources)
- [ ] Użytkownicy (pracownicy) - cl. `User`
   - [ ] Dodawanie/usuwanie/edytowanie
   - [ ] Tymczasowa dezaktywacja konta
- [ ] Role (stanowiska) - cl. `Role`
   - [ ] Dodawanie/usuwanie/edytowanie

cl. - nazwa klasy przedstawiająca dany obiekt
*(komentarz) - możliwe do zmiany przez klienta w panelu, w komentarzu sposób zarządzania (np. on/off, czas)