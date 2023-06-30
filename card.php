<!DOCTYPE html>
<html>
<head>
    <title>Validar número de tarjeta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            text-align: center;
        }

        form {
            text-align: center;
            margin-top: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"] {
            padding: 5px;
            font-size: 16px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        .card-info {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .card-info h2 {
            margin-bottom: 10px;
        }

        .card-info p {
            margin: 5px 0;
        }

        .card-info .country-info {
            margin-top: 10px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }

        .card-info .country-info h3 {
            margin-bottom: 5px;
        }

        .card-info .country-info p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>Validar número de tarjeta</h1>

    <form method="POST" action="">
        <label for="cardNumber">Ingrese el número de tarjeta:</label>
        <input type="text" name="cardNumber" id="cardNumber">
        <input type="submit" value="Validar">
    </form>

    <?php
    session_start(); // Iniciar sesión

    function calculateCheckDigit($cardNumber) {
        $length = strlen($cardNumber) - 1;
        $sum = 0;
        $parity = $length % 2;
    
        for ($i = $length; $i >= 0; $i--) {
            $digit = $cardNumber[$i] - '0';
    
            if ($i % 2 == $parity) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
    
            $sum += $digit;
        }
    
        return (10 - ($sum % 10)) % 10;
    }
    
    function validateCardNumber($cardNumber) {
        $length = strlen($cardNumber);
        $checkDigit = intval($cardNumber[$length - 1]);
    
        $numberWithoutCheckDigit = substr($cardNumber, 0, $length - 1);
    
        $calculatedCheckDigit = calculateCheckDigit($numberWithoutCheckDigit);
    
        return $checkDigit == $calculatedCheckDigit;
    }
    
    function getCardInfo($bin) {
        $url = "https://lookup.binlist.net/$bin";
        $options = array(
            "http" => array(
                "header" => "Accept-Version: 3",
            ),
        );
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        return $data;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cardNumber = $_POST['cardNumber'];
        if ($cardNumber == NULL || strlen($cardNumber) < 6) {
            echo "<div class='card-info'>";
            echo "<h2>Resultado:</h2>";
            echo "<p>Ingrese un número de tarjeta válido.</p>";
            echo "</div>";
            return;
        }
        
        // Eliminar cualquier espacio en blanco al inicio o al final
        $cardNumber = trim($cardNumber);

        if (!validateCardNumber($cardNumber)) {
            echo "<div class='card-info'>";
            echo "<h2>Resultado:</h2>";
            echo "<p>El número de tarjeta es inválido.</p>";
            echo "</div>";
        } else {
            $bin = substr($cardNumber, 0, 6);
            $cardInfo = getCardInfo($bin);

            echo "<div class='card-info'>";
            echo "<h2>Resultado:</h2>";
            echo "<p>El número de tarjeta es válido.</p>";
            echo "<h2>Datos de la tarjeta:</h2>";
            echo "<p>Número de tarjeta: " . ($cardNumber) . "</p>";

            if (($cardInfo['scheme'] ?? '')) {
                echo "<p>Franquicia: " . ($cardInfo['scheme'] ?? '') . "</p>";
            } else {
                echo "<p>Franquicia: N/A</p>";
            }

            if (($cardInfo['type'] ?? '')) {
                echo "<p>Tipo: " . ($cardInfo['type'] ?? '') . "</p>";
            } else {
                echo "<p>Tipo: N/A</p>";
            }

            if (($cardInfo['brand'] ?? '')) {
                echo "<p>Marca: " . ($cardInfo['brand'] ?? '') . "</p>";
            } else {
                echo "<p>Marca: N/A</p>";
            }

            if (($cardInfo['bank']['name'] ?? '')) {
                echo "<p>Banco: " . ($cardInfo['bank']['name'] ?? '') . "</p>";
            } else {
                echo "<p>Banco: N/A</p>";
            }

            echo "<div class='country-info'>";
            echo "<h3>Información del país:</h3>";

            if (($cardInfo['country']['name'] ?? '')) {
                echo "<p>País: " . ($cardInfo['country']['name'] ?? '') . " " . ($cardInfo['country']['emoji'] ?? '') . "</p>";
            } else {
                echo "<p>País: N/A</p>";
            }

            if (($cardInfo['country']['numeric'] ?? '')) {
                echo "<p>Código Numérico: " . ($cardInfo['country']['numeric'] ?? '') . "</p>";
            } else {
                echo "<p>Código Numérico: N/A</p>";
            }

            if (($cardInfo['country']['alpha2'] ?? '')) {
                echo "<p>Código Alfa-2: " . ($cardInfo['country']['alpha2'] ?? '') . "</p>";
            } else {
                echo "<p>Código Alfa-2: N/A</p>";
            }

            if (($cardInfo['country']['currency'] ?? '')) {
                echo "<p>Moneda: " . ($cardInfo['country']['currency'] ?? '') . "</p>";
            } else {
                echo "<p>Moneda: N/A</p>";
            }

            if (($cardInfo['country']['latitude'] ?? '')) {
                echo "<p>Latitud: " . ($cardInfo['country']['latitude'] ?? '') . "</p>";
            } else {
                echo "<p>Latitud: N/A</p>";
            }

            if (($cardInfo['country']['longitude'] ?? '')) {
                echo "<p>Longitud: " . ($cardInfo['country']['longitude'] ?? '') . "</p>";
            } else {
                echo "<p>Longitud: N/A</p>";
            }

            echo "</div>"; // Close country-info div
            echo "</div>"; // Close card-info div
            
            echo "® Creado por Yeison Ramirez";
        }
        
    }
    ?>
  
</body>
</html>