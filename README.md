# PQCMS
Wcale nie PoLeqCMS, tylko Professional Quality CMS

# WAŻNE Zawrzeć BBCode w edytorze
tutaj coś jest: https://github.com/chriskonnertz/bbcode

# MODUŁY (Funkcjonalności)

## Logowanie
- [x] Sesja użytkownika
- [ ] Logi logowań

## Dane
- [x] Podstawowe dane
   - [x] przechowywane w pliku json
   - [x] APIki do pobierania oraz edycji odpowiednich danych
   - [x] Zabezpieczenie przed nieautoryzowanym dostępem

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
- [x] Użytkownicy (pracownicy) - cl. `User`
   - [ ] Dodawanie/usuwanie/edytowanie
   - [x] Tymczasowa dezaktywacja konta
- [ ] Role (stanowiska) - cl. `Role`
   - [ ] Dodawanie/usuwanie/edytowanie

cl. - nazwa klasy przedstawiająca dany obiekt\
*(komentarz) - możliwe do zmiany przez klienta w panelu, w komentarzu sposób zarządzania (np. on/off, czas)