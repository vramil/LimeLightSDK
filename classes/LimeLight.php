<?php

namespace almeyda\limelightsdk\classes;

class LimeLight
{
    protected $baseurl;
    protected $fullurl;
    protected $method;
    protected $output_type;
    protected $password;
    protected $response;
    protected $username;

    // throws exception on database failure
    function __construct($api_username, $api_password, $lime_light_url, $output_as = 'array')
    {
        $this->baseurl = $lime_light_url;

        $this->username = $api_username;
        $this->password = $api_password;

        $this->output_type = 'array';
    }    //	END __construct()

    protected function APIConnect($fields, $values)
    {
        $api_conn = null;
        $api_post = null;
        $ch = null;
        $cr = null;
        $ct = null;
        $fv = null;
        $i = null;

        $api_conn = array(
            'username' => $this->username,
            'password' => $this->password,
            'method' => $this->method
        );

        // check parameters
        if (is_array($fields) && is_array($values)) {
            // parameters are arrays
            $ct = count($fields);

            for ($i = 0; $i < $ct; $i++) {
                $fv[$fields[$i]] = $values[$i];
            }

            $api_post = array_merge($api_conn, $fv);
        } else {
            if ($fields != '' && $values != '') {
                // parameters are non-empty strings
                $api_post = array_merge($api_conn, array($fields => $values));
            } else {
                // parameters are empty strings
                $api_post = $api_conn;
            }
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $api_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $this->fullurl);

        $x = print_r(curl_exec($ch), true);

        return ($cr = curl_exec($ch));
    }    // END APIConnect()

    protected function ArrayPopulate($number_of_elements, $populate_value = '')
    {
        $array_variable = null;
        $i = null;

        for ($i = 0; $i < $number_of_elements; $i++) {
            $array_variable[$i] = $populate_value;
        }    // END for loop

        return ($array_variable);
    }    // END ArrayPopulate()

    protected function AssociativeArrayToArray($associative_array)
    {
        $array_out = null;
        $i = null;
        $v = null;

        $i = 0;

        // convert associative array to non-associative
        foreach ($associative_array as $v) {
            $array_out[$i] = $v;
            $i++;
        }

        return $array_out;
    }

    protected function WriteXML(XMLWriter $xml, $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $xml->startElement($key);
                $this->Write($xml, $value);
                $xml->endElement();
                continue;
            }
            $xml->writeElement($key, urldecode($value));
        }
    }    // END WriteXML()

    public function GetArray($data_string)
    {
        parse_str($data_string, $arr);

        return $arr;
    }    // END GetArray()

    public function GetXML($data_array)
    {
        $xml = null;
        $xml_encoding = null;
        $xml_version = null;

        $xml_encoding = 'UTF-8';
        $xml_version = '1.0';

        $xml = new XMLWriter();

        if (!is_array($data_array)) {
            return false;
        } else {
            $xml->openMemory();
            $xml->startDocument($xml_version, $xml_encoding, 'yes');
            $this->WriteXML($xml, $data_array);
            $xml->endDocument();

            return $xml->outputMemory(true);
        }
    }    // END GetXML()

}    // END class LimeLight
