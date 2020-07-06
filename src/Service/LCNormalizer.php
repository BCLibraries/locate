<?php

namespace App\Service;

/**
 * Normalize Library of Congress call numbers
 *
 * This class normalizes LC Call Numbers for searching and sorting. It uses the normalization
 * scheme that Bill Dueber developed at Michigan, converting call numbers like:
 *
 *    E                          E  0000.0000  0000  0000
 *    E 184 .A1 G78              E  0184.0000A 1000G 7800
 *    E184.A2 G78 1967           E  0184.0000A 2000G 7800 1967
 *    E184.A2 G78 1970           E  0184.0000A 2000G 7800 1970
 *    EA                         EA 0000.0000  0000  0000
 *    EA 10                      EA 0010.0000  0000  0000
 *    EA 10 1970                 EA 0010.0000  0000  0000 1970
 *    EA10 B7                    EA 0010.0000B 7000  0000
 *    EA 10.B7.G8                EA 0010.0000B 7000G 8000
 *    EA10.5                     EA 0010.5000  0000  0000
 *
 * We've broken Bill's one long regular expression into several smaller regular expressions
 * that we consume one part at a time, mostly because long regular expressions are hard
 * to read and hard to think about.
 *
 * See: http://robotlibrarian.billdueber.com/2008/11/normalizing-loc-call-numbers-for-sorting/
 *
 * @package App\Service
 */
class LCNormalizer implements CallNumberNormalizer
{
    /**
     * The call number string. Processed from left to right. As rules
     * are applied, matching parts are removed.
     *
     * @var string
     */
    private $call_number_string;

    /**
     * Stores components of call number
     *
     * @var CallNumber
     */
    private $parsed_call_number;

    /**
     * Matches the letters at the start of the call number
     */
    private const ALPHA_REGEX = '/
                                   ^\s*            
                                   ([A-Z]{1,3})    # Capture one to three letters
                                 /x';

    /**
     * Match the optional numbers
     */
    private const NUMBER_REGEX = '/
                                    ^\s*
                                      (\d+                 # Capture number
                                         (?:\s*\.\s*\d*)?  # Optional decimal, possibly with spaces
                                      )?                   # Number is optional
                                  /x';

    /**
     * Matches a cutter
     */
    private const CUTTER_REGEX = '/
                                    ^\s*          
                                    (?:\.?\s*     # Starting period (optional) 
                                      ([A-Z]+)    # Cutter letter (required if there is a cutter)
                                      \s*(\d+)?   # Cutter number (optional)
                                    )?            # Cutter is optional
                                  /x';

    /**
     * Matches stuff at the end, like FOLIO or 1973
     */
    private const EXTRA_REGEX = '/
                                    ^\s*(.*)\s*$ # Capture anything but surrounding spaces
                                 /x';

    /**
     * Generate a normalized call number
     *
     * @param string $call_number
     * @param bool $is_row_end is this call number at the end of a row?
     * @return string
     */
    public function normalize(string $call_number, bool $is_row_end = false): string
    {
        # Strip spaces from call number.
        $this->call_number_string = $call_number;
        $this->parsed_call_number = new CallNumber();

        # Row end cutters should default to Z9999 to capture call numbers with the
        # same alpha and numeric components. If the shelf is split at a cutter, the
        # subsequent rules will replace these cutters.
        if ($is_row_end) {
            $this->parsed_call_number->cutter_1_number = '9999';
            $this->parsed_call_number->cutter_1_alpha = 'Z';
            $this->parsed_call_number->cutter_2_number = '9999';
            $this->parsed_call_number->cutter_2_alpha = 'Z';
        }

        # Apply the rules, chopping off each component from the front of the string
        # as it is matched.
        $this->nextToken(self::ALPHA_REGEX, ['alpha']);
        $this->nextToken(self::NUMBER_REGEX, ['number']);
        $this->nextToken(self::CUTTER_REGEX, ['cutter_1_alpha', 'cutter_1_number']);
        $this->nextToken(self::CUTTER_REGEX, ['cutter_2_alpha', 'cutter_2_number']);
        $this->nextToken(self::EXTRA_REGEX, ['extra']);

        # Return the formatted output.
        return $this->format($this->parsed_call_number);
    }

    /**
     * Match a call number component
     *
     * Match the next call number component, add its values to the current call
     * number object, and strip it from the current call number text.
     *
     * @param string $regex a regular expression with one or more capture groups
     * @param string[] $attribute_names the CallNumber attributes to store the captured data, in order of capture
     */
    private function nextToken(string $regex, array $attribute_names): void
    {
        $component = null;

        # Look for matches to the rule and populate the attributes if you find
        # any matches.
        $matched = preg_match($regex, $this->call_number_string, $matches);
        if ($matched !== false) {
            array_shift($matches);
            $this->assignAttributes($matches, $attribute_names);
        }

        # Strip this token from the call number for further processing.
        $this->call_number_string = preg_replace($regex, '', $this->call_number_string);
    }

    /**
     * Assign a list of values to a list of call number attributes
     *
     * @param string[] $values
     * @param string[] $attribute_names
     */
    private function assignAttributes(array $values, array $attribute_names): void
    {
        // Pad out the values array so it has an entry for every attribute.
        $values = array_pad($values, count($attribute_names), null);

        // Build a hash with attribute names as keys and values as values.
        $attribute_value_hash = array_combine($attribute_names, $values);

        // Assign each value to the respective attribute.
        foreach ($attribute_value_hash as $attribute_name => $value) {
            if ($value !== null) {
                $this->parsed_call_number->$attribute_name = $value;
            }
        }

    }

    /**
     * Format the normalized call number for output
     *
     * @param CallNumber $call_number
     * @return string
     */
    private function format(CallNumber $call_number): string
    {
        return sprintf("%-3s%09.4f%-2s%4s%-2s%4s%s",
            $call_number->alpha,
            $call_number->number,
            $call_number->cutter_1_alpha,
            $this->formatCutterNumber($call_number->cutter_1_number),
            $call_number->cutter_2_alpha,
            $this->formatCutterNumber($call_number->cutter_2_number),
            $call_number->extra ? " {$call_number->extra}" : '');     # Add a space before extra content, if any
    }

    /**
     * Format cutter numbers
     *
     * @param string|null $cutter_number
     * @return string
     */
    private function formatCutterNumber(?string $cutter_number): string
    {
        # Right-pad number with zeroes.
        $padded_cutter_number = str_pad($cutter_number, 4, '0');

        # Only take the first four digits
        return substr($padded_cutter_number, 0, 4);
    }
}