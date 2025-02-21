<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<label>FIRST NAME :</label><br>
    <input type="text" name="prenom" value="$prenom" required><br>
    <label>LAST NAME :</label><br>
    <input type="text" name="nom" value="$nom" required><br>
    <label>EMAIL : </label><br>
    <input type="email" name="email" value="$email" required><br>
    <label>BIRTHDAY :       </label>
    <input type="date" name="birthday" value="$birthday"required><br>
   
    <div class="options">
            <p>Choisissez votre dernier Dîplome :</p>
            <label>
                <input type="radio" name="option" value="BACALAUREAT" required>
                BACALAUREAT
            </label>
            <label>
                <input type="radio" name="option" value="DEUST">
                DEUST
            </label>
            <label>
                <input type="radio" name="option" value="LICENCE">
                LICENCE
            </label>
        </div>
        

    </form>
</body>
</html>