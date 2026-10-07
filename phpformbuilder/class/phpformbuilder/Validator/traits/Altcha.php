<?php

declare(strict_types=1);

namespace phpformbuilder\Validator\traits;

trait Altcha
{
    /**
     * Validate the altcha response.
     *
     * @param string|null $message The error message to be displayed. Default is null.
     * @return \phpformbuilder\Validator
     */
    /**
     * Validate the altcha response.
     *
     * @param string|null $message The error message to be displayed. Default is null.
     * @return \phpformbuilder\Validator\Validator
     */
    public function altcha($message = null): self
    {
        $this->setRule(__FUNCTION__, function ($altcha) {
            if (empty($altcha)) {
                return false;
            } else {
                // load the altcha API key
                require_once \dirname(__DIR__, 4) . '/plugins/altcha/altcha_api_key.php';

                $altchaData = json_decode(base64_decode($altcha));

                // Check if decoding was successful and the necessary properties exist
                if ($altchaData === null || !isset($altchaData->algorithm, $altchaData->signature)) {
                    return false; // Decoding failed or required properties are missing
                }

                $expectedAlgorithm = 'SHA-256';

                // Validate the algorithm
                $algOk = $altchaData->algorithm === $expectedAlgorithm;

                // if the Altacha Spam Filter is enabled
                if (isset($altchaData->verificationData)) {
                    // calculate SHA hash of the `verificationData`, unparsed, as they appear in the payload
                    $hash = hash('sha256', $altchaData->verificationData, true);
                    $signature = hash_hmac('sha256', $hash, ALTCHA_API_SECRET, true);

                    // Convert to hex for string comparison (optional)
                    $signatureHex = bin2hex($signature);

                    // Validate the signature
                    $signatureOk = hash_equals($altchaData->signature, $signatureHex);

                    // Determine overall verification status
                    return $algOk && $signatureOk;
                } else {
                    // Validate the challenge
                    $challengeOk = isset($altchaData->challenge) && isset($altchaData->salt) && isset($altchaData->number) && hash_equals($altchaData->challenge, hash('sha256', $altchaData->salt . $altchaData->number));

                    // Validate the signature
                    $signatureOk = isset($altchaData->challenge) && hash_equals($altchaData->signature, hash_hmac('sha256', $altchaData->challenge, ALTCHA_API_SECRET));

                    // Determine overall verification status
                    return $algOk && $challengeOk && $signatureOk;
                }
            }
        }, $message);

        return $this;
    }

    /**
     * Retrieves the spam filter data for the Altcha validation.
     *
     * @param string $altcha The Altcha value to retrieve spam filter data for.
     * @return array<mixed> The spam filter data array for the Altcha validation.
     */
    public function getAltchaSpamFilterData(string $altcha)
    {
        if (isset($_POST[$altcha]) && !empty($_POST[$altcha])) {
            $altchaData = json_decode(base64_decode($_POST[$altcha]));
            if ($altchaData !== null && isset($altchaData->algorithm, $altchaData->signature, $altchaData->verificationData)) {
                // convert the GET string to an array
                parse_str($altchaData->verificationData, $verificationData);

                // convert the score to a float
                $verificationData['score'] = (float) $verificationData['score'];

                $this->altchaSpamFilterData = $verificationData;

                return $verificationData;
            }
            // trigger a php warning with the posted altcha value and the $altchaData variable object converted to a string
            trigger_error('The Altcha value "' . $_POST[$altcha] . '" could not be decoded. The Altcha data object is: ' . print_r($altchaData, true), E_USER_WARNING);

            return [];
        }

        // if the altcha value is not set or empty
        return ['score' => 10];
    }

    public function getAltchaSpamFilterErrorMessage(): string
    {
        $spamFilterData = $this->altchaSpamFilterData;

        if (isset($spamFilterData['classification'], $spamFilterData['score'], $spamFilterData['reasons'])) {

            $errorMessage = $this->getAltchaDefaultErrorMessage('classified_as_spam');

            if ($spamFilterData['classification'] === 'BAD') {
                $errorMessage .= '<br>' . $this->getAltchaDefaultErrorMessage('score');

                if (!empty($spamFilterData['reasons'])) {
                    $errorMessage .= '<br>' . $this->getAltchaDefaultErrorMessage('reasons');
                }
            }

            return $errorMessage;
        }

        return $this->getAltchaDefaultErrorMessage('classified_as_spam');
    }

    protected function getAltchaDefaultErrorMessage(string $key): string
    {
        // https://altcha.org/docs/api/spam-filter-api/#text-rules
        /*  text rules:                     email rules
            CAPITALIZATION                  FREE_PROVIDER
            CURRENCY                        DMARC
            EMOJI                           MX
            EXCLAMATION                     REPORTED
            HASH_TAGS                       INVALID
            HTML
            HTML_INJECTION                  IP Address Rules
            NUMBERS_ONLY                    BLOCKED_COUNTRY
            PROFANITY                       HOSTING
            RANDOM_CHARS                    MALICIOUS
            SENTIMENT                       PROXY
            SHORT_TEXT                      TOR
            SPAM_WORDS                      UNEXPECTED_COUNTRY
            SPECIAL_CHARS
            SQL_INJECTION                   Time-zone Rules
            UNEXPECTED_LANGUAGE             BLOCKED_COUNTRY
            UNKNOWN_LANGUAGE                UNEXPECTED_COUNTRY
            URL
        */
        $message = '';
        if ($this->language == 'de') {
            // German
            switch ($key) {
                case 'classified_as_spam':
                    $message = 'Der Altcha Spam-Filter hat Ihre Einreichung als Spam klassifiziert.';
                    break;

                case 'score':
                    $message = 'Die Punktzahl beträgt ' . $this->altchaSpamFilterData['score'] . '.';
                    break;

                case 'reasons':
                    $reasons = explode(',', $this->altchaSpamFilterData['reasons']);
                    $reasons = array_map(function ($reason) {
                        return $this->getAltchaDefaultErrorMessage(str_replace(['text.', 'email.'], '', $reason));
                    }, $reasons);

                    $message = 'Die Gründe sind:<br>' . implode('<br>', $reasons) . '.';
                    break;

                case 'CAPITALIZATION':
                    $message = 'Der Text enthält übermäßig viele Großbuchstaben.';
                    break;

                case 'CURRENCY':
                    $message = 'Der Text enthält Währungssymbole.';
                    break;

                case 'EMOJI':
                    $message = 'Der Text enthält Emojis.';
                    break;

                case 'EXCLAMATION':
                    $message = 'Der Text enthält übermäßig viele Ausrufezeichen.';
                    break;

                case 'HASH_TAGS':
                    $message = 'Der Text enthält Hashtags.';
                    break;

                case 'HTML':
                    $message = 'Der Text enthält HTML-Tags.';
                    break;

                case 'HTML_INJECTION':
                    $message = 'Der Text enthält Muster für HTML-Injection.';
                    break;

                case 'NUMBERS_ONLY':
                    $message = 'Der Text besteht nur aus Zahlen.';
                    break;

                case 'PROFANITY':
                    $message = 'Der Text enthält anstößige Sprache.';
                    break;

                case 'RANDOM_CHARS':
                    $message = 'Der Text enthält zufällige Zeichen.';
                    break;

                case 'SENTIMENT':
                    $message = 'Der Text hat eine negative Stimmung.';
                    break;

                case 'SHORT_TEXT':
                    $message = 'Der Text ist zu kurz.';
                    break;

                case 'SPAM_WORDS':
                    $message = 'Der Text enthält Spam-Schlüsselwörter.';
                    break;

                case 'SPECIAL_CHARS':
                    $message = 'Der Text enthält Sonderzeichen.';
                    break;

                case 'SQL_INJECTION':
                    $message = 'Der Text enthält Muster für SQL-Injection.';
                    break;

                case 'UNKNOWN_LANGUAGE':
                    $message = 'Der Text ist in einer unbekannten Sprache.';
                    break;

                case 'URL':
                    $message = 'Der Text enthält URLs.';
                    break;

                case 'FREE_PROVIDER':
                    $message = 'Die E-Mail stammt von einem kostenlosen E-Mail-Anbieter.';
                    break;

                case 'DMARC':
                    $message = 'Die E-Mail scheitert an der DMARC-Authentifizierung.';
                    break;

                case 'MX':
                    $message = 'Die E-Mail hat einen ungültigen MX-Eintrag.';
                    break;

                case 'REPORTED':
                    $message = 'Die E-Mail wurde als Spam gemeldet.';
                    break;

                case 'INVALID':
                    $message = 'Die E-Mail ist ungültig.';
                    break;

                case 'BLOCKED_COUNTRY':
                    $message = 'Die E-Mail stammt aus einem gesperrten Land.';
                    break;

                case 'HOSTING':
                    $message = 'Die E-Mail stammt von einem Hosting-Anbieter.';
                    break;

                case 'MALICIOUS':
                    $message = 'Die E-Mail wird als bösartig markiert.';
                    break;

                case 'PROXY':
                    $message = 'Die E-Mail wird über einen Proxy-Server gesendet.';
                    break;

                case 'TOR':
                    $message = 'Die E-Mail wird über das Tor-Netzwerk gesendet.';
                    break;

                case 'UNEXPECTED_COUNTRY':
                    $message = 'Die E-Mail stammt aus einem unerwarteten Land.';
                    break;

                default:
                    $message = '';
                    break;
            }
        } elseif ($this->language == 'es') {
            // Spanish
            switch ($key) {
                case 'classified_as_spam':
                    $message = 'El filtro de spam de Altcha ha clasificado su envío como spam.';
                    break;

                case 'score':
                    $message = 'La puntuación es ' . $this->altchaSpamFilterData['score'] . '.';
                    break;

                case 'reasons':
                    $reasons = explode(',', $this->altchaSpamFilterData['reasons']);
                    $reasons = array_map(function ($reason) {
                        return $this->getAltchaDefaultErrorMessage(str_replace(['text.', 'email.'], '', $reason));
                    }, $reasons);

                    $message = 'Las razones son:<br>' . implode('<br>', $reasons) . '.';
                    break;

                case 'CAPITALIZATION':
                    $message = 'El texto contiene demasiadas mayúsculas.';
                    break;

                case 'CURRENCY':
                    $message = 'El texto contiene símbolos de moneda.';
                    break;

                case 'EMOJI':
                    $message = 'El texto contiene emojis.';
                    break;

                case 'EXCLAMATION':
                    $message = 'El texto contiene demasiados signos de exclamación.';
                    break;

                case 'HASH_TAGS':
                    $message = 'El texto contiene hashtags.';
                    break;

                case 'HTML':
                    $message = 'El texto contiene etiquetas HTML.';
                    break;

                case 'HTML_INJECTION':
                    $message = 'El texto contiene patrones de inyección HTML.';
                    break;

                case 'NUMBERS_ONLY':
                    $message = 'El texto solo contiene números.';
                    break;

                case 'PROFANITY':
                    $message = 'El texto contiene lenguaje ofensivo.';
                    break;

                case 'RANDOM_CHARS':
                    $message = 'El texto contiene caracteres aleatorios.';
                    break;

                case 'SENTIMENT':
                    $message = 'El texto tiene un sentimiento negativo.';
                    break;

                case 'SHORT_TEXT':
                    $message = 'El texto es demasiado corto.';
                    break;

                case 'SPAM_WORDS':
                    $message = 'El texto contiene palabras clave de spam.';
                    break;

                case 'SPECIAL_CHARS':
                    $message = 'El texto contiene caracteres especiales.';
                    break;

                case 'SQL_INJECTION':
                    $message = 'El texto contiene patrones de inyección SQL.';
                    break;

                case 'UNKNOWN_LANGUAGE':
                    $message = 'El texto está en un idioma desconocido.';
                    break;

                case 'URL':
                    $message = 'El texto contiene URLs.';
                    break;

                case 'FREE_PROVIDER':
                    $message = 'El correo electrónico proviene de un proveedor de correo electrónico gratuito.';
                    break;

                case 'DMARC':
                    $message = 'El correo electrónico falla en la autenticación DMARC.';
                    break;

                case 'MX':
                    $message = 'El correo electrónico tiene una entrada MX inválida.';
                    break;

                case 'REPORTED':
                    $message = 'El correo electrónico ha sido reportado como spam.';
                    break;

                case 'INVALID':
                    $message = 'El correo electrónico es inválido.';
                    break;

                case 'BLOCKED_COUNTRY':
                    $message = 'El correo electrónico proviene de un país bloqueado.';
                    break;

                case 'HOSTING':
                    $message = 'El correo electrónico proviene de un proveedor de hosting.';
                    break;

                case 'MALICIOUS':
                    $message = 'El correo electrónico está marcado como malicioso.';
                    break;

                case 'PROXY':
                    $message = 'El correo electrónico se envía a través de un servidor proxy.';
                    break;

                case 'TOR':
                    $message = 'El correo electrónico se envía a través de la red Tor.';
                    break;

                case 'UNEXPECTED_COUNTRY':
                    $message = 'El correo electrónico proviene de un país inesperado.';
                    break;

                default:
                    $message = '';
                    break;
            }
        } elseif ($this->language == 'fr') {
            // French
            switch ($key) {
                case 'classified_as_spam':
                    $message = 'Le filtre anti-spam Altcha a classé votre soumission comme spam.';
                    break;

                case 'score':
                    $message = 'Le score est ' . $this->altchaSpamFilterData['score'] . '.';
                    break;

                case 'reasons':
                    $reasons = explode(',', $this->altchaSpamFilterData['reasons']);
                    $reasons = array_map(function ($reason) {
                        return $this->getAltchaDefaultErrorMessage(str_replace(['text.', 'email.'], '', $reason));
                    }, $reasons);

                    $message = 'Les raisons sont :<br>' . implode('<br>', $reasons) . '.';
                    break;

                case 'CAPITALIZATION':
                    $message = 'Le texte contient trop de majuscules.';
                    break;

                case 'CURRENCY':
                    $message = 'Le texte contient des symboles monétaires.';
                    break;

                case 'EMOJI':
                    $message = 'Le texte contient des emojis.';
                    break;

                case 'EXCLAMATION':
                    $message = 'Le texte contient trop de points d\'exclamation.';
                    break;

                case 'HASH_TAGS':
                    $message = 'Le texte contient des hashtags.';
                    break;

                case 'HTML':
                    $message = 'Le texte contient des balises HTML.';
                    break;

                case 'HTML_INJECTION':
                    $message = 'Le texte contient des motifs d\'injection HTML.';
                    break;

                case 'NUMBERS_ONLY':
                    $message = 'Le texte ne contient que des chiffres.';
                    break;

                case 'PROFANITY':
                    $message = 'Le texte contient un langage offensant.';
                    break;

                case 'RANDOM_CHARS':
                    $message = 'Le texte contient des caractères aléatoires.';
                    break;

                case 'SENTIMENT':
                    $message = 'Le texte a un ton négatif.';
                    break;

                case 'SHORT_TEXT':
                    $message = 'Le texte est trop court.';
                    break;

                case 'SPAM_WORDS':
                    $message = 'Le texte contient des mots-clés de spam.';
                    break;

                case 'SPECIAL_CHARS':
                    $message = 'Le texte contient des caractères spéciaux.';
                    break;

                case 'SQL_INJECTION':
                    $message = 'Le texte contient des motifs d\'injection SQL.';
                    break;

                case 'UNKNOWN_LANGUAGE':
                    $message = 'Le texte est dans une langue inconnue.';
                    break;

                case 'URL':
                    $message = 'Le texte contient des URL.';
                    break;

                case 'FREE_PROVIDER':
                    $message = 'L\'email provient d\'un fournisseur de messagerie gratuit.';
                    break;

                case 'DMARC':
                    $message = 'L\'email échoue à l\'authentification DMARC.';
                    break;

                case 'MX':
                    $message = 'L\'email a un enregistrement MX invalide.';
                    break;

                case 'REPORTED':
                    $message = 'L\'email a été signalé comme spam.';
                    break;

                case 'INVALID':
                    $message = 'L\'email est invalide.';
                    break;

                case 'BLOCKED_COUNTRY':
                    $message = 'L\'email provient d\'un pays bloqué.';
                    break;

                case 'HOSTING':
                    $message = 'L\'email provient d\'un fournisseur d\'hébergement.';
                    break;

                case 'MALICIOUS':
                    $message = 'L\'email est marqué comme malveillant.';
                    break;

                case 'PROXY':
                    $message = 'L\'email est envoyé via un serveur proxy.';
                    break;

                case 'TOR':
                    $message = 'L\'email est envoyé via le réseau Tor.';
                    break;

                case 'UNEXPECTED_COUNTRY':
                    $message = 'L\'email provient d\'un pays inattendu.';
                    break;

                default:
                    $message = '';
                    break;
            }
        } elseif ($this->language == 'it') {
            // Italian
            switch ($key) {
                case 'classified_as_spam':
                    $message = 'Il filtro spam di Altcha ha classificato il tuo invio come spam.';
                    break;

                case 'score':
                    $message = 'Il punteggio è ' . $this->altchaSpamFilterData['score'] . '.';
                    break;

                case 'reasons':
                    $reasons = explode(',', $this->altchaSpamFilterData['reasons']);
                    $reasons = array_map(function ($reason) {
                        return $this->getAltchaDefaultErrorMessage(str_replace(['text.', 'email.'], '', $reason));
                    }, $reasons);

                    $message = 'Le ragioni sono:<br>' . implode('<br>', $reasons) . '.';
                    break;

                case 'CAPITALIZATION':
                    $message = 'Il testo contiene troppe maiuscole.';
                    break;

                case 'CURRENCY':
                    $message = 'Il testo contiene simboli di valuta.';
                    break;

                case 'EMOJI':
                    $message = 'Il testo contiene emoji.';
                    break;

                case 'EXCLAMATION':
                    $message = 'Il testo contiene troppi punti esclamativi.';
                    break;

                case 'HASH_TAGS':
                    $message = 'Il testo contiene hashtag.';
                    break;

                case 'HTML':
                    $message = 'Il testo contiene tag HTML.';
                    break;

                case 'HTML_INJECTION':
                    $message = 'Il testo contiene pattern di iniezione HTML.';
                    break;

                case 'NUMBERS_ONLY':
                    $message = 'Il testo contiene solo numeri.';
                    break;

                case 'PROFANITY':
                    $message = 'Il testo contiene linguaggio offensivo.';
                    break;

                case 'RANDOM_CHARS':
                    $message = 'Il testo contiene caratteri casuali.';
                    break;

                case 'SENTIMENT':
                    $message = 'Il testo ha un tono negativo.';
                    break;

                case 'SHORT_TEXT':
                    $message = 'Il testo è troppo corto.';
                    break;

                case 'SPAM_WORDS':
                    $message = 'Il testo contiene parole chiave spam.';
                    break;

                case 'SPECIAL_CHARS':
                    $message = 'Il testo contiene caratteri speciali.';
                    break;

                case 'SQL_INJECTION':
                    $message = 'Il testo contiene pattern di iniezione SQL.';
                    break;

                case 'UNKNOWN_LANGUAGE':
                    $message = 'Il testo è in una lingua sconosciuta.';
                    break;

                case 'URL':
                    $message = 'Il testo contiene URL.';
                    break;

                case 'FREE_PROVIDER':
                    $message = 'L\'email proviene da un fornitore gratuito.';
                    break;

                case 'DMARC':
                    $message = 'L\'email fallisce l\'autenticazione DMARC.';
                    break;

                case 'MX':
                    $message = 'L\'email ha un record MX non valido.';
                    break;

                case 'REPORTED':
                    $message = 'L\'email è stata segnalata come spam.';
                    break;

                case 'INVALID':
                    $message = 'L\'email è invalida.';
                    break;

                case 'BLOCKED_COUNTRY':
                    $message = 'L\'email proviene da un paese bloccato.';
                    break;

                case 'HOSTING':
                    $message = 'L\'email proviene da un fornitore di hosting.';
                    break;

                case 'MALICIOUS':
                    $message = 'L\'email è marcata come malevola.';
                    break;

                case 'PROXY':
                    $message = 'L\'email è inviata tramite un server proxy.';
                    break;

                case 'TOR':
                    $message = 'L\'email è inviata tramite la rete Tor.';
                    break;

                case 'UNEXPECTED_COUNTRY':
                    $message = 'L\'email proviene da un paese inaspettato.';
                    break;

                default:
                    $message = '';
                    break;
            }
        } elseif ($this->language == 'pt_br') {
            // Brazilian Portuguese
            switch ($key) {
                case 'classified_as_spam':
                    $message = 'O filtro de spam Altcha classificou sua submissão como spam.';
                    break;

                case 'score':
                    $message = 'A pontuação é ' . $this->altchaSpamFilterData['score'] . '.';
                    break;

                case 'reasons':
                    $reasons = explode(',', $this->altchaSpamFilterData['reasons']);
                    $reasons = array_map(function ($reason) {
                        return $this->getAltchaDefaultErrorMessage(str_replace(['text.', 'email.'], '', $reason));
                    }, $reasons);

                    $message = 'Os motivos são:<br>' . implode('<br>', $reasons) . '.';
                    break;

                case 'CAPITALIZATION':
                    $message = 'O texto contém muitas letras maiúsculas.';
                    break;

                case 'CURRENCY':
                    $message = 'O texto contém símbolos de moeda.';
                    break;

                case 'EMOJI':
                    $message = 'O texto contém emojis.';
                    break;

                case 'EXCLAMATION':
                    $message = 'O texto contém muitos pontos de exclamação.';
                    break;

                case 'HASH_TAGS':
                    $message = 'O texto contém hashtags.';
                    break;

                case 'HTML':
                    $message = 'O texto contém tags HTML.';
                    break;

                case 'HTML_INJECTION':
                    $message = 'O texto contém padrões de injeção HTML.';
                    break;

                case 'NUMBERS_ONLY':
                    $message = 'O texto contém apenas números.';
                    break;

                case 'PROFANITY':
                    $message = 'O texto contém linguagem ofensiva.';
                    break;

                case 'RANDOM_CHARS':
                    $message = 'O texto contém caracteres aleatórios.';
                    break;

                case 'SENTIMENT':
                    $message = 'O texto tem um tom negativo.';
                    break;

                case 'SHORT_TEXT':
                    $message = 'O texto é muito curto.';
                    break;

                case 'SPAM_WORDS':
                    $message = 'O texto contém palavras-chave de spam.';
                    break;

                case 'SPECIAL_CHARS':
                    $message = 'O texto contém caracteres especiais.';
                    break;

                case 'SQL_INJECTION':
                    $message = 'O texto contém padrões de injeção SQL.';
                    break;

                case 'UNKNOWN_LANGUAGE':
                    $message = 'O texto está em uma língua desconhecida.';
                    break;

                case 'URL':
                    $message = 'O texto contém URLs.';
                    break;

                case 'FREE_PROVIDER':
                    $message = 'O email vem de um provedor gratuito.';
                    break;

                case 'DMARC':
                    $message = 'O email falha na autenticação DMARC.';
                    break;

                case 'MX':
                    $message = 'O email tem um registro MX inválido.';
                    break;

                case 'REPORTED':
                    $message = 'O email foi reportado como spam.';
                    break;

                case 'INVALID':
                    $message = 'O email é inválido.';
                    break;

                case 'BLOCKED_COUNTRY':
                    $message = 'O email vem de um país bloqueado.';
                    break;

                case 'HOSTING':
                    $message = 'O email vem de um provedor de hospedagem.';
                    break;

                case 'MALICIOUS':
                    $message = 'O email é marcado como malicioso.';
                    break;

                case 'PROXY':
                    $message = 'O email é enviado através de um servidor proxy.';
                    break;

                case 'TOR':
                    $message = 'O email é enviado através da rede Tor.';
                    break;

                case 'UNEXPECTED_COUNTRY':
                    $message = 'O email vem de um país inesperado.';
                    break;

                default:
                    $message = '';
                    break;
            }
        } elseif ($this->language == 'ru') {
            // Russian
            switch ($key) {
                case 'classified_as_spam':
                    $message = 'Фильтр спама Altcha классифицировал ваше сообщение как спам.';
                    break;

                case 'score':
                    $message = 'Оценка составляет ' . $this->altchaSpamFilterData['score'] . '.';
                    break;

                case 'reasons':
                    $reasons = explode(',', $this->altchaSpamFilterData['reasons']);
                    $reasons = array_map(function ($reason) {
                        return $this->getAltchaDefaultErrorMessage(str_replace(['text.', 'email.'], '', $reason));
                    }, $reasons);

                    $message = 'Причины следующие:<br>' . implode('<br>', $reasons) . '.';
                    break;

                case 'CAPITALIZATION':
                    $message = 'Текст содержит слишком много заглавных букв.';
                    break;

                case 'CURRENCY':
                    $message = 'Текст содержит символы валют.';
                    break;

                case 'EMOJI':
                    $message = 'Текст содержит эмодзи.';
                    break;

                case 'EXCLAMATION':
                    $message = 'Текст содержит слишком много восклицательных знаков.';
                    break;

                case 'HASH_TAGS':
                    $message = 'Текст содержит хэштеги.';
                    break;

                case 'HTML':
                    $message = 'Текст содержит HTML-теги.';
                    break;

                case 'HTML_INJECTION':
                    $message = 'Текст содержит паттерны HTML-инъекций.';
                    break;

                case 'NUMBERS_ONLY':
                    $message = 'Текст состоит только из чисел.';
                    break;

                case 'PROFANITY':
                    $message = 'Текст содержит оскорбительную лексику.';
                    break;

                case 'RANDOM_CHARS':
                    $message = 'Текст содержит случайные символы.';
                    break;

                case 'SENTIMENT':
                    $message = 'Текст имеет негативный тон.';
                    break;

                case 'SHORT_TEXT':
                    $message = 'Текст слишком короткий.';
                    break;

                case 'SPAM_WORDS':
                    $message = 'Текст содержит спам-слова.';
                    break;

                case 'SPECIAL_CHARS':
                    $message = 'Текст содержит специальные символы.';
                    break;

                case 'SQL_INJECTION':
                    $message = 'Текст содержит паттерны SQL-инъекций.';
                    break;

                case 'UNKNOWN_LANGUAGE':
                    $message = 'Текст на неизвестном языке.';
                    break;

                case 'URL':
                    $message = 'Текст содержит URL.';
                    break;

                case 'FREE_PROVIDER':
                    $message = 'Электронная почта с бесплатного почтового сервиса.';
                    break;

                case 'DMARC':
                    $message = 'Электронная почта не прошла аутентификацию DMARC.';
                    break;

                case 'MX':
                    $message = 'У электронной почты неверная запись MX.';
                    break;

                case 'REPORTED':
                    $message = 'Электронная почта была отмечена как спам.';
                    break;

                case 'INVALID':
                    $message = 'Электронная почта недействительна.';
                    break;

                case 'BLOCKED_COUNTRY':
                    $message = 'Электронная почта из заблокированной страны.';
                    break;

                case 'HOSTING':
                    $message = 'Электронная почта с сервера хостинга.';
                    break;

                case 'MALICIOUS':
                    $message = 'Электронная почта помечена как вредоносная.';
                    break;

                case 'PROXY':
                    $message = 'Электронная почта отправлена через прокси-сервер.';
                    break;

                case 'TOR':
                    $message = 'Электронная почта отправлена через сеть Tor.';
                    break;

                case 'UNEXPECTED_COUNTRY':
                    $message = 'Электронная почта из неожиданной страны.';
                    break;

                default:
                    $message = '';
                    break;
            }
        } elseif ($this->language == 'ua') {
            // Ukrainian
            switch ($key) {
                case 'classified_as_spam':
                    $message = 'Фільтр спаму Altcha класифікував ваше повідомлення як спам.';
                    break;

                case 'score':
                    $message = 'Оцінка складає ' . $this->altchaSpamFilterData['score'] . '.';
                    break;

                case 'reasons':
                    $reasons = explode(',', $this->altchaSpamFilterData['reasons']);
                    $reasons = array_map(function ($reason) {
                        return $this->getAltchaDefaultErrorMessage(str_replace(['text.', 'email.'], '', $reason));
                    }, $reasons);

                    $message = 'Причини наступні:<br>' . implode('<br>', $reasons) . '.';
                    break;

                case 'CAPITALIZATION':
                    $message = 'Текст містить занадто багато великих літер.';
                    break;

                case 'CURRENCY':
                    $message = 'Текст містить символи валют.';
                    break;

                case 'EMOJI':
                    $message = 'Текст містить емодзі.';
                    break;

                case 'EXCLAMATION':
                    $message = 'Текст містить занадто багато знаків оклику.';
                    break;

                case 'HASH_TAGS':
                    $message = 'Текст містить хештеги.';
                    break;

                case 'HTML':
                    $message = 'Текст містить HTML-теги.';
                    break;

                case 'HTML_INJECTION':
                    $message = 'Текст містить шаблони HTML-ін\'єкцій.';
                    break;

                case 'NUMBERS_ONLY':
                    $message = 'Текст складається лише з чисел.';
                    break;

                case 'PROFANITY':
                    $message = 'Текст містить образливу лексику.';
                    break;

                case 'RANDOM_CHARS':
                    $message = 'Текст містить випадкові символи.';
                    break;

                case 'SENTIMENT':
                    $message = 'Текст має негативний тон.';
                    break;

                case 'SHORT_TEXT':
                    $message = 'Текст занадто короткий.';
                    break;

                case 'SPAM_WORDS':
                    $message = 'Текст містить спам-слова.';
                    break;

                case 'SPECIAL_CHARS':
                    $message = 'Текст містить спеціальні символи.';
                    break;

                case 'SQL_INJECTION':
                    $message = 'Текст містить шаблони SQL-ін\'єкцій.';
                    break;

                case 'UNKNOWN_LANGUAGE':
                    $message = 'Текст на невідомій мові.';
                    break;

                case 'URL':
                    $message = 'Текст містить URL.';
                    break;

                case 'FREE_PROVIDER':
                    $message = 'Електронна пошта з безкоштовного поштового сервісу.';
                    break;

                case 'DMARC':
                    $message = 'Електронна пошта не пройшла аутентифікацію DMARC.';
                    break;

                case 'MX':
                    $message = 'У електронної пошти неправильний запис MX.';
                    break;

                case 'REPORTED':
                    $message = 'Електронну пошту було позначено як спам.';
                    break;

                case 'INVALID':
                    $message = 'Електронна пошта недійсна.';
                    break;

                case 'BLOCKED_COUNTRY':
                    $message = 'Електронна пошта з заблокованої країни.';
                    break;

                case 'HOSTING':
                    $message = 'Електронна пошта з сервера хостингу.';
                    break;

                case 'MALICIOUS':
                    $message = 'Електронна пошта позначена як шкідлива.';
                    break;

                case 'PROXY':
                    $message = 'Електронна пошта відправлена через проксі-сервер.';
                    break;

                case 'TOR':
                    $message = 'Електронна пошта відправлена через мережу Tor.';
                    break;

                case 'UNEXPECTED_COUNTRY':
                    $message = 'Електронна пошта з неочікуваної країни.';
                    break;

                default:
                    $message = '';
                    break;
            }
        } else {
            // Default English
            switch ($key) {
                case 'classified_as_spam':
                    $message = 'The Altcha Spam Filter has classified your submission as spam.';
                    break;

                case 'score':
                    $message = 'The score is ' . $this->altchaSpamFilterData['score'] . '.';
                    break;

                case 'reasons':
                    $reasons = explode(',', $this->altchaSpamFilterData['reasons']);
                    $reasons = array_map(function ($reason) {
                        return $this->getAltchaDefaultErrorMessage(str_replace(['text.', 'email.'], '', $reason));
                    }, $reasons);
                    $message = 'The reasons are: <br>' . implode('<br>', $reasons) . '.';
                    break;

                case 'CAPITALIZATION':
                    $message = 'The text contains excessive capitalization.';
                    break;

                case 'CURRENCY':
                    $message = 'The text contains currency symbols.';
                    break;

                case 'EMOJI':
                    $message = 'The text contains emojis.';
                    break;

                case 'EXCLAMATION':
                    $message = 'The text contains excessive exclamation marks.';
                    break;

                case 'HASH_TAGS':
                    $message = 'The text contains hashtags.';
                    break;

                case 'HTML':
                    $message = 'The text contains HTML tags.';
                    break;

                case 'HTML_INJECTION':
                    $message = 'The text contains HTML injection patterns.';
                    break;

                case 'NUMBERS_ONLY':
                    $message = 'The text contains only numbers.';
                    break;

                case 'PROFANITY':
                    $message = 'The text contains profane language.';
                    break;

                case 'RANDOM_CHARS':
                    $message = 'The text contains random characters.';
                    break;

                case 'SENTIMENT':
                    $message = 'The text has negative sentiment.';
                    break;

                case 'SHORT_TEXT':
                    $message = 'The text is too short.';
                    break;

                case 'SPAM_WORDS':
                    $message = 'The text contains spam keywords.';
                    break;

                case 'SPECIAL_CHARS':
                    $message = 'The text contains special characters.';
                    break;

                case 'SQL_INJECTION':
                    $message = 'The text contains SQL injection patterns.';
                    break;

                case 'UNKNOWN_LANGUAGE':
                    $message = 'The text is in an unknown language.';
                    break;

                case 'URL':
                    $message = 'The text contains URLs.';
                    break;

                case 'FREE_PROVIDER':
                    $message = 'The email is from a free email provider.';
                    break;

                case 'DMARC':
                    $message = 'The email fails DMARC authentication.';
                    break;

                case 'MX':
                    $message = 'The email has an invalid MX record.';
                    break;

                case 'REPORTED':
                    $message = 'The email has been reported as spam.';
                    break;

                case 'INVALID':
                    $message = 'The email is invalid.';
                    break;

                case 'BLOCKED_COUNTRY':
                    $message = 'The email is from a blocked country.';
                    break;

                case 'HOSTING':
                    $message = 'The email is from a hosting provider.';
                    break;

                case 'MALICIOUS':
                    $message = 'The email is flagged as malicious.';
                    break;

                case 'PROXY':
                    $message = 'The email is sent through a proxy server.';
                    break;

                case 'TOR':
                    $message = 'The email is sent through the Tor network.';
                    break;

                case 'UNEXPECTED_COUNTRY':
                    $message = 'The email is from an unexpected country.';
                    break;

                default:
                    $message = '';
                    break;
            }
        }

        return $message;
    }
}
