# PQCMS - API:

# WAŻNE!!!! - do APIków dodać sprawdzenie, czy nie ma przerwy technicznej

Spis treści:
- [API - Akcje](#pqcms---api-akcje)
  - system
    - [GetClientVersion](#getclientversion)
    - [GetServerVersion](#getserverversion)
  - website
    - data
      - hr
        - admin
          - DoesAminExists
        - GetUser
      - license
        - [VerifyLicense](#verifylicense-wyjątek-post)
      - settings
        - [GetSettings](#getsettings)
        - [UpdateSettings](#updatesettings)



Klient korzystając z /pqcms/Communicator.inc.php wykunuje wszelkie akcje.\
\
**Communicator zawsze odczytuje z POST:** *domain, secure_key (+dodatkowe)*\
\
**Wyjątkiem jest weryfikacja licensji - odczytywany jest domain, login, license_key**

# PQCMS - API: Akcje
#### Wszelkie API ma wymagane 2 posty (**[wyjątek](#verifylicense-wyjątek-post)**):
- domain *(string, max 253)* - domena, z której wysłane zostało zapytanie
- secure_key *(string, 128)* - klucz, który pozwala na wykonanie zapytania API

```json
{
  "domain": "localhost",
  "secure_key": "1c4922116fe96f7565e5fd3b..."
}
```

W dokumentacji, jeżeli przy POST znajdzie się <b>(?)</b> oznacza to, że dany argument jest opcjonalny.



### VerifyLicense (Wyjątek: POST)
<details>
    <summary><b>(!)</b> POST</summary>

```json
{
  "domain": "localhost",
  "login": "l0c@lh0st!", 
  "license_key": "2023-11-09 20:07:35",
  "generate_secure_key": true
}
```
Gdzie:
<ul>
  <li>domain: <i>string (max 253)</i></li>
  <li>login: <i>string (max 30)</i></li>
  <li>license_key: <i>string (23)</i></li>
  <li><b>(?)</b> generate_secure_key: <i>bool</i></li>
</ul>

  </details>
<details>
  <summary>RETURN</summary>

  - <details>
      <summary>Dla nieudanej weryfikacji</summary>

    ```json
    {
      "suc": 0,
      "desc": "Autoryzacja nie powiodła się.",
      "tries_left": 4
    }
    ```
    **tries_left** *(int)* - ilość prób, która pozostała się IP z requesta
    </details>
  - <details>
      <summary>Bez <i>generate_secure_key</i> (lub jego wartości = false)</summary>
    
    ```json
    {
      "suc": 1,
      "desc": "Autoryzacja powiodła się!", 
      "expiry_date": "2023-11-09 20:07:35"
    }
    ```
    **expiry_date** może być null — oznacza to, że licencja jest nieograniczona czasowo
    </details>
  - <details>
      <summary>Dla <i>generate_secure_key</i> = true</summary>
    
    ```json
    {
      "suc": 1,
      "desc": "Autoryzacja powiodła się!", 
      "expiry_date": "2023-11-09 20:07:35",
      "secure_key": "1c4922116fe96f7565e5fd3b..."
    }
    ```
    **expiry_date** może być null — oznacza to, że licencja jest nieograniczona czasowo\
    **secure_key** — jednorazowy klucz dostępowy do API. Oznacza to możliwość wykonania 1 operacji
    na API za jego pomocą (później jest unieważniany)
    </details>
</details>



### GetClientVersion
<details>
    <summary>POST</summary>

```json
{
  "domain": "localhost",
  "secure_key": "1c4922116fe96f7565e5fd3b...",
  "complex": true
}
```
Gdzie:
- [default](#wszelkie-api-ma-wymagane-2-posty-wyjątekverifylicense-wyjątek-post) *(domain, secure_key)*
- <b>(?)</b> complex <i>(bool)</i>
  - Dla wartości **true** — zwróci wersję w formie dokładnej
  - Dla wartości **false** (lub null) — zwróci wersję w formie tekstu
  </details>
<details>
  <summary>RETURN</summary>

- <details>
  <summary>Wersja "dokładna"</summary>

  ```json
  {
    "suc": 1,
    "version": 
    {
      "type": "alpha",
      "major": 1,
      "minor": 0,
      "patch": 0,
      "date": 
      {
        "year": 2023,
        "month": 10,
        "day": 27
      }
    }
  }
  ```
  </details>
- <details>
  <summary>Wersja "tekstu"</summary>

  ```json
  {
    "suc": 1,
    "version": "alpha-1.0.0 (2023-10-27)"
  }
  ```

  Wyjaśnienie: <b>type-major.minor.patch (year-month-day)</b>
  </details>
</details>

### GetServerVersion
<details>
    <summary>POST</summary>

```json
{
  "domain": "localhost",
  "secure_key": "1c4922116fe96f7565e5fd3b...",
  "complex": true
}
```
Gdzie:
  - [default](#wszelkie-api-ma-wymagane-2-posty-wyjątekverifylicense-wyjątek-post) *(domain, secure_key)*
  - <b>(?)</b> complex <i>(bool)</i>
    - Dla wartości **true** — zwróci wersję w formie dokładnej
    - Dla wartości **false** (lub null) — zwróci wersję w formie tekstu
  </details>
<details>
  <summary>RETURN</summary>

  - <details>
    <summary>Wersja "dokładna"</summary>

    ```json
    {
      "suc": 1,
      "version": 
      {
        "type": "alpha",
        "major": 1,
        "minor": 0,
        "patch": 0,
        "date":
        {
          "year": 2023,
          "month": 11,
          "day": 15
        }      
      }
    }
    ```
    </details>
  - <details>
    <summary>Wersja "tekstu"</summary>

    ```json
    {
      "suc": 1,
      "version": "alpha-1.0.0 (2023-11-15)" 
    }
    ```
    Wyjaśnienie: <b>type-major.minor.patch (year-month-day)</b>
  </details>
</details>

### DoesAdminExists
<details>
    <summary>POST</summary>

```json
{
  "domain": "localhost",
  "secure_key": "1c4922116fe96f7565e5fd3b..."
}
```
Gdzie:
- [default](#wszelkie-api-ma-wymagane-2-posty-wyjątekverifylicense-wyjątek-post) *(domain, secure_key)*
  </details>
<details>
  <summary>RETURN</summary>

  ```json
  {
    "suc": 1,
    "resp": 1
  }
  ```
  **"resp"** — response, zwraca wartość 1|0
- 1 - admin strony instnieje
- 0 - admin strony nie instnieje 
</details>

### LoginUser
<details>
    <summary>POST</summary>

```json
{
  "domain": "localhost",
  "secure_key": "1c4922116fe96f7565e5fd3b...",
  "login": "admin123",
  "password": "admin123"
}
```
Gdzie:
  - [default](#wszelkie-api-ma-wymagane-2-posty-wyjątekverifylicense-wyjątek-post) *(domain, secure_key)*
  - login: string (max: 30)
  - password (string: max: 50)
  </details>
<details>
  <summary>RETURN</summary>

  ```json
  {
    "suc": 1,
    "resp": 1,
    "desc": "Pomyślnie zalogowano!",
    "auth_key": "1c4922116fe96f7565e5fd3b..."
  }
  ```
  **"resp"** — response, zwraca wartość 1|0
  - 1 - zalogowano
  - 0 - nie zalogowano

  **"auth_key"** *(string, 128)* — klucz logowania, identyfikuje sesje logowania
  Jeśli użytkownik jest już zalogowany (`auth_key` jest jeszcze ważne):

  ```json
  {
    "suc": 1,
    "resp": 1,
    "desc": "Pomyślnie zalogowano!",
    "auth_key": "1c4922116fe96f7565e5fd3b..."
  }
```
</details>



### GetSettings
<details>
    <summary>POST</summary>

```json
{
  "domain": "localhost",
  "secure_key": "1c4922116fe96f7565e5fd3b..."
}
```
Gdzie:
  - [default](#wszelkie-api-ma-wymagane-2-posty-wyjątekverifylicense-wyjątek-post) *(domain, secure_key)*
  </details>
<details>
  <summary>RETURN</summary>

  ```json
  {
    "suc": 1,
    "resp":
    {
      "login_attempts": 5,
      "token_lifespan": 600
    }
  }
  ```
  **"resp"** — response, zwraca wartości danych (podane wyżej są domyślne)
  - login_attempts <i>(int)</i> - liczba prób do logowania na IP na dobę (po nieudanym logowaniu ofc.)
    - 0 - zablokowane (w sumie to wynika, logika)
    - \> 1 - dana ilość 
  - token_lifespan <i>(int)</i> - czas żywotności tokenu CSRF (w sekundach)
</details>


### UpdateSettings
<details>
    <summary>POST</summary>

```json
{
  "domain": "localhost",
  "secure_key": "1c4922116fe96f7565e5fd3b...",
  "login_count": 5,
  "token_lifespan": 600,
  "login_count_reset": 0,
  "token_lifespan_reset": 1
}
```
Gdzie:
  - [default](#wszelkie-api-ma-wymagane-2-posty-wyjątekverifylicense-wyjątek-post) *(domain, secure_key)*
  - login_count <i>(int)</i> - ilość prób logowań na IP na dobę
  - token_lifespan <i>(int)</i> - czas żywotności tokenu CSRF (w sekundach)
  - login_count_reset <i>(bool)</i> - (jeśli true) reset login_count do wartości defaultowej
  - token_lifespan_reset <i>(bool)</i> - (jeśli true) reset token_lifespan do wartości defaultowej
  </details>
<details>
  <summary>RETURN</summary>

  ```json
  {
    "suc": 1,
    "resp": 1,
    "desc": "Zmieniono ustawienia strony!"
  }
  ```
  **"resp"** — response, zwraca, czy zmieniono dane (jak na razie to zawsze jest true XD, bo dla suc: 0 nie występuje)
</details>








\
\
\
\
\
\
\
\
\
\
- <sub>A,U,G</sub>AdminAccount *update mógłbym zrobić, że dopiero po tel. do mnie odblokowuje jednorazowy/czasowy update*
- <sub>A,U,G,D</sub>UserAccount
- <sub>G</sub>SecureKey