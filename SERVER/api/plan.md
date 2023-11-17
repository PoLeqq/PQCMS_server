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
        - [VerifyLicense](#verifylicense-wyjątek)



Klient korzystając z /pqcms/Communicator.inc.php wykunuje wszelkie akcje.\
\
**Communicator zawsze odczytuje z POST:** *domain, secure_key (+dodatkowe)*\
\
**Wyjątkiem jest weryfikacja licensji - odczytywany jest domain, login, license_key**

# PQCMS - API: Akcje
Definicje: A - add, G - get, U - update, D - delete

### VerifyLicense (Wyjątek)
- POST
  - domain
  - login
  - license_key
- return
  - **suc: [0,1]**
  - **desc: {string: description}**
  - **tries_left: {int: tries amount for ip}** (dla suc: 0)

### ServerVersion
- POST
  - domain
  - login
  - license_key
- return
  - **suc: [0,1]**
  - **desc: {string: description}**
  - **tries_left: {int: tries amount for ip}** (dla suc: 0)

- <sub>G</sub>ClientVersion
- <sub>A,U,G</sub>AdminAccount *update mógłbym zrobić, że dopiero po tel. do mnie odblokowuje jednorazowy/czasowy update*\
- <sub>A,U,G,D</sub>UserAccount\
- <sub>G</sub>SecureKey\