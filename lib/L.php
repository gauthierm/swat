<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Silverorange\Swat;

/**
 * String Tools
 *
 * @package   Swat
 * @copyright 2005-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class L
{
    // {{{ constants

    /**
     * The gettext domain for Swat
     *
     * This is used to support multiple locales.
     */
    const GETTEXT_DOMAIN = 'swat';

    // }}}
    // {{{ private properties

    /**
     * Whether or not the gettext domain is set up
     *
     * @var boolean
     */
    private static $set_up = false;

    // }}}
    // {{{ public static function _()

    /**
     * Translates a phrase
     *
     * This is an alias for {@link L::gettext()}.
     *
     * @param string $message the phrase to be translated.
     *
     * @return string the translated phrase.
     */
    public static function _($message)
    {
        return self::gettext($message);
    }

    // }}}
    // {{{ public static function gettext()

    /**
     * Translates a phrase
     *
     * This method relies on the php gettext extension and uses dgettext()
     * internally.
     *
     * @param string $message the phrase to be translated.
     *
     * @return string the translated phrase.
     */
    public static function gettext($message)
    {
        self::setup();
        return dgettext(self::GETTEXT_DOMAIN, $message);
    }

    // }}}
    // {{{ public static function ngettext()

    /**
     * Translates a plural phrase
     *
     * This method should be used when a phrase depends on a number. For
     * example, use ngettext when translating a dynamic phrase like:
     *
     * - "There is 1 new item" for 1 item and
     * - "There are 2 new items" for 2 or more items.
     *
     * This method relies on the php gettext extension and uses dngettext()
     * internally.
     *
     * @param string  $singular_message the message to use when the number the
     *                                  phrase depends on is one.
     * @param string  $plural_message   the message to use when the number the
     *                                  phrase depends on is more than one.
     * @param integer $number           the number the phrase depends on.
     *
     * @return string the translated phrase.
     */
    public static function ngettext($singular_message, $plural_message, $number)
    {
        self::setup();
        return dngettext(
            self::GETTEXT_DOMAIN,
            $singular_message,
            $plural_message,
            $number
        );
    }

    // }}}
    // {{{ private static function setup()

    private static function setup()
    {
        if (!self::$set_up) {
            $path = '@DATA-DIR@/Swat/locale';
            if ($path[0] === '@') {
                $path = __DIR__ . '/../locale';
            }

            bindtextdomain(self::GETTEXT_DOMAIN, $path);
            bind_textdomain_codeset(self::GETTEXT_DOMAIN, 'UTF-8');

            self::$set_up = true;
        }
    }

    // }}}
    // {{{ private function __construct()

    /**
     * Don't allow instantiation of this object
     *
     * This class contains only static methods and should not be instantiated.
     */
    private function __construct()
    {
    }

    // }}}
}
