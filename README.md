## Refactoring

### 1. HTMLPurifier in createVerein.php entfernt: 
Wurde bereits in in createVerein() ausgeführt und gehört auch dort hin!

### 2. Parameter in editUser.php verringert:
Daten als Array übergeben statt einzeln ($array statt $vorname, $nachname, etc.)