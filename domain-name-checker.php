<?php header('Content-Type: application/json');

/**
 * Class DomainChecker
 * @author Jaziel Lopez <juan.jaziel@gmail.com>
 * @url https://github.com/jazlopez/yadnc
 * @desc Yet another domain name checker
 */
class DomainChecker
{

    /**
     *
     */
    const EXT_COM = '.com';

    /**
     *
     */
    const EXT_NET = '.net';

    /**
     *
     */
    const EXT_ORG = '.org';

    /**
     *
     */
    const EXT_US = '.us';

    /**
     *
     */
    const EXT_BIZ = '.biz';

    /**
     *
     */
    const EXT_INFO = '.info';

    /**
     *
     */
    const EXT_MX = '.mx';

    /**
     *
     */
    const COM_NOT_FOUND = 'no match for';

    /**
     *
     */
    const NET_NOT_FOUND = 'no match for';

    /**
     *
     */
    const ORG_NOT_FOUND = 'not found';

    /**
     *
     */
    const US_NOT_FOUND = 'not found';

    /**
     *
     */
    const BIZ_NOT_FOUND = 'not found';

    /**
     *
     */
    const INFO_NOT_FOUND = 'not found';

    /**
     *
     */
    const MX_NOT_FOUND = 'no_se_encontro_el_objeto/object_not_found';

    /**
     *
     */
    const WHOIS_URL_COM = 'whois.crsnic.net';

    /**
     *
     */
    const WHOIS_URL_NET = 'whois.crsnic.net';

    /**
     *
     */
    const WHOIS_URL_ORG = 'whois.publicinterestregistry.net';

    /**
     *
     */
    const WHOIS_URL_US = 'whois.nic.us';

    /**
     *
     */
    const WHOIS_URL_BIZ = 'whois.nic.biz';

    /**
     *
     */
    const WHOIS_URL_INFO = 'whois.afilias.net';

    /**
     *
     */
    const WHOIS_URL_MX = 'whois.nic.mx';

    /**
     * Get whois raw
     * @param $base
     * @param $whois
     * @param int $port
     * @return string
     * @throws Exception
     */
    static function raw($base, $whois, $port = 43)
    {
        // Open a socket connection to the whois server
        $socket = fsockopen($whois, $port);

        if(!$socket)
            throw new \Exception(sprintf('Connection to %s cannot be established'. $whois));

        fputs($socket, $base . "\r\n");

        // Read and store the server response
        $raw = '';

        while (!feof($socket)) $raw .= fgets($socket);

        fclose($socket);

        return $raw;
    }

    /**
     * Parse not found messages
     * @param $raw
     * @param $subject
     * @return bool
     */
    static function parse($raw, $subject)
    {

        $matches = array_map(function($item) use ($raw) {

            return stristr($raw, $item);

        }, explode('/', $subject));


        return in_array(true, $matches);
    }

    /**
     * Score
     * @param $domain
     * @param $server
     * @param $subject
     * @return string
     */
    static function score($domain, $server, $subject)
    {

        return self::parse(self::raw($domain, $server), $subject) ? 'available' : 'taken';
    }
}

$eCode    = 0;  // exit code

$httpCode = 200; // http code

try{

    if(empty($_REQUEST['name']))
        throw new \Exception('Domain name cannot be empty');

    $base = strtolower($_REQUEST['name']);

    /**
     * Domain names cannot have more than 63 characters, not including .AG, .COM.AG, .NET.AG, .ORG.AG, .EDU.AG, .GOV.AG, ETC.
     * Maximum length of a complete (Fully Qualified, FQDN) domain name (including .separators) is 255 characters.
     * Minimum length of a domain name is 1 character, not including extensions.
     * http://www.nic.ag/rules.htm
    **/
    if (!(strlen($base) > 0))
        throw new \Exception('Provided domain name is not a valid name. Minimum length of a domain name is 1 character.');

    if (strlen($base) > 63)
        throw new \Exception('Provided domain name is not a valid name. Domain names cannot have more than 63 characters.');

    $extensions = [
        ['extension' => DomainChecker::EXT_COM, 'whois' => DomainChecker::WHOIS_URL_COM, 'needle' => DomainChecker::COM_NOT_FOUND],
        ['extension' => DomainChecker::EXT_NET, 'whois' => DomainChecker::WHOIS_URL_NET, 'needle' => DomainChecker::NET_NOT_FOUND],
        ['extension' => DomainChecker::EXT_ORG, 'whois' => DomainChecker::WHOIS_URL_ORG, 'needle' => DomainChecker::ORG_NOT_FOUND],
        ['extension' => DomainChecker::EXT_US, 'whois' => DomainChecker::WHOIS_URL_US, 'needle' => DomainChecker::US_NOT_FOUND],
        ['extension' => DomainChecker::EXT_BIZ, 'whois' => DomainChecker::WHOIS_URL_BIZ, 'needle' => DomainChecker::BIZ_NOT_FOUND],
        ['extension' => DomainChecker::EXT_INFO, 'whois' => DomainChecker::WHOIS_URL_INFO, 'needle' => DomainChecker::INFO_NOT_FOUND],
    ];

    $data = array_map(function($tld) use ($base) {

        $domainName = $base . $tld['extension'];

        return sprintf('%s is %s', $domainName, DomainChecker::score($domainName, $tld['whois'] , $tld['needle']));

    }, $extensions);

    $output = ['response' => $data];

}catch(\Exception $e) {

    $httpCode = 400;

    $eCode++;

    $output = ['error' => sprintf('%s, %s:%d', $e->getMessage(), $e->getFile(), $e->getLine())];
}

http_response_code($httpCode);

echo \json_encode($output);

exit($eCode);
