# PQCMS - API:

Spis treści:
- [API - Akcje](#pqcms---api-akcje)
  - system
    - GetClientVersion
    - GetServerVersion
  - website
    - data
      - hr
        - GetAdmin
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
- W dokumentacji dalej, jeżeli przy POST znajdzie się <b style="color: aqua">(?)</b> oznacza to, że dany argument jest opcjonalny

### VerifyLicense (Wyjątek: POST)
- <details>
    <summary><b style="color: orangered">(!)</b> POST</summary>
    <ul>
      <li>domain: string (max 253)</li>
      <li>login: string (max 30)</li>
      <li>license_key: string (23)</li>
    </ul>
  </details>
- <details>
    <summary>return</summary>
  
    ```json
      {
        "suc": 0|1,
        "desc": "opis",
        "tries_left": (int) tries amount for ip (only for suc = 0)
      }
    ```
    **tries_left** - ilość prób, która pozostała się IP z requesta
  </details>

### GetServerVersion
- POST
  - default
    - <b>(?) complex *(bool)*
      - <details>
        <summary>Dla wartości <b>true</b></summary>
    
        Zwróci wersję w formie dokładnej
    
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
        <summary>Dla wartości <b>false</b></summary>
        Zwróci wersję w formie tekstu <i>(string)</i>

          ```
            alpha-1.0.0 (2023-10-27)
          ```
        </details>
- return

  

### GetClientVersion
- <sub>A,U,G</sub>AdminAccount *update mógłbym zrobić, że dopiero po tel. do mnie odblokowuje jednorazowy/czasowy update*\
- <sub>A,U,G,D</sub>UserAccount\
- <sub>G</sub>SecureKey\
