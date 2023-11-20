# PQCMS - API:

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



Klient korzystając z /pqcms/Communicator.inc.php wykunuje wszelkie akcje.\
\
**Communicator zawsze odczytuje z POST:** *domain, secure_key (+dodatkowe)*\
\
**Wyjątkiem jest weryfikacja licensji - odczytywany jest domain, login, license_key**

# PQCMS - API: Akcje
Wszelkie API ma wymagane 2 posty (**[wyjątek](#verifylicense-wyjątek-post)**):
- domain *(string, max 253)* - domena, z której wysłane zostało zapytanie
- secure_key *(string, 128)* - klucz, który pozwala na wykonanie zapytania API
- W dokumentacji dalej, jeżeli przy POST znajdzie się <b>(?)</b> oznacza to, że dany argument jest opcjonalny

### VerifyLicense (Wyjątek: POST)
<details>
    <summary><b>(!)</b> POST</summary>
    <ul>
      <li>domain: string (max 253)</li>
      <li>login: string (max 30)</li>
      <li>license_key: string (23)</li>
      <li><b>(?)</b> generate_secure_key: bool</li>
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
    **expiry_date** może być null — oznacza to, że licencja jest nieograniczona czasowo
    **secure_key** — jednorazowy klucz dostępowy do API. Oznacza to możliwość wykonania 1 operacji
    na API za jego pomocą (później jest unieważniany)
    </details>
</details>

### GetClientVersion
<details>
    <summary>POST</summary>

- default *(domain, secure_key)*
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
  ```
  </details>
- <details>
  <summary>Wersja "tekstu"</summary>

  ```
  alpha-1.0.0 (2023-10-27)
  ```
  Wyjaśnienie: <b>type-major.minor.patch (year-month-day)</b>
  </details>
</details>

### GetServerVersion
<details>
    <summary>POST</summary>

  - default *(domain, secure_key)*
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
    ```
    </details>
  - <details>
    <summary>Wersja "tekstu"</summary>

    ```
    alpha-1.0.0 (2023-11-15)
    ```
    Wyjaśnienie: <b>type-major.minor.patch (year-month-day)</b>
  </details>
</details>

### DoesAdminExists
<details>
    <summary>POST</summary>

  - default *(domain, secure_key)*
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
</details>

### LoginUser
<details>
    <summary>POST</summary>

  - default *(domain, secure_key)*
  - login: string (max: 30)
  - password (string: max: 50)
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
</details>


- <sub>A,U,G</sub>AdminAccount *update mógłbym zrobić, że dopiero po tel. do mnie odblokowuje jednorazowy/czasowy update*\
- <sub>A,U,G,D</sub>UserAccount\
- <sub>G</sub>SecureKey\
