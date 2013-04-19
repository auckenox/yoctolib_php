<?php
/*********************************************************************
 *
 * $Id: yocto_hubport.php 9979 2013-02-22 13:45:33Z seb $
 *
 * Implements yFindHubPort(), the high-level API for HubPort functions
 *
 * - - - - - - - - - License information: - - - - - - - - - 
 *
 * Copyright (C) 2011 and beyond by Yoctopuce Sarl, Switzerland.
 *
 * 1) If you have obtained this file from www.yoctopuce.com,
 *    Yoctopuce Sarl licenses to you (hereafter Licensee) the
 *    right to use, modify, copy, and integrate this source file
 *    into your own solution for the sole purpose of interfacing
 *    a Yoctopuce product with Licensee's solution.
 *
 *    The use of this file and all relationship between Yoctopuce 
 *    and Licensee are governed by Yoctopuce General Terms and 
 *    Conditions.
 *
 *    THE SOFTWARE AND DOCUMENTATION ARE PROVIDED 'AS IS' WITHOUT
 *    WARRANTY OF ANY KIND, EITHER EXPRESS OR IMPLIED, INCLUDING 
 *    WITHOUT LIMITATION, ANY WARRANTY OF MERCHANTABILITY, FITNESS 
 *    FOR A PARTICULAR PURPOSE, TITLE AND NON-INFRINGEMENT. IN NO
 *    EVENT SHALL LICENSOR BE LIABLE FOR ANY INCIDENTAL, SPECIAL,
 *    INDIRECT OR CONSEQUENTIAL DAMAGES, LOST PROFITS OR LOST DATA, 
 *    COST OF PROCUREMENT OF SUBSTITUTE GOODS, TECHNOLOGY OR 
 *    SERVICES, ANY CLAIMS BY THIRD PARTIES (INCLUDING BUT NOT 
 *    LIMITED TO ANY DEFENSE THEREOF), ANY CLAIMS FOR INDEMNITY OR
 *    CONTRIBUTION, OR OTHER SIMILAR COSTS, WHETHER ASSERTED ON THE
 *    BASIS OF CONTRACT, TORT (INCLUDING NEGLIGENCE), BREACH OF
 *    WARRANTY, OR OTHERWISE.
 *
 * 2) If your intent is not to interface with Yoctopuce products,
 *    you are not entitled to use, read or create any derived
 *    material from this source file.
 *
 *********************************************************************/


//--- (return codes)
//--- (end of return codes)
//--- (YHubPort definitions)
if(!defined('Y_ENABLED_FALSE')) define('Y_ENABLED_FALSE', 0);
if(!defined('Y_ENABLED_TRUE')) define('Y_ENABLED_TRUE', 1);
if(!defined('Y_ENABLED_INVALID')) define('Y_ENABLED_INVALID', -1);
if(!defined('Y_PORTSTATE_OFF')) define('Y_PORTSTATE_OFF', 0);
if(!defined('Y_PORTSTATE_ON')) define('Y_PORTSTATE_ON', 1);
if(!defined('Y_PORTSTATE_RUN')) define('Y_PORTSTATE_RUN', 2);
if(!defined('Y_PORTSTATE_INVALID')) define('Y_PORTSTATE_INVALID', -1);
if(!defined('Y_LOGICALNAME_INVALID')) define('Y_LOGICALNAME_INVALID', Y_INVALID_STRING);
if(!defined('Y_ADVERTISEDVALUE_INVALID')) define('Y_ADVERTISEDVALUE_INVALID', Y_INVALID_STRING);
if(!defined('Y_BAUDRATE_INVALID')) define('Y_BAUDRATE_INVALID', Y_INVALID_UNSIGNED);
//--- (end of YHubPort definitions)

/**
 * YHubPort Class: Yocto-hub port interface
 * 
 * 
 */
class YHubPort extends YFunction
{
    //--- (YHubPort implementation)
    const LOGICALNAME_INVALID = Y_INVALID_STRING;
    const ADVERTISEDVALUE_INVALID = Y_INVALID_STRING;
    const ENABLED_FALSE = 0;
    const ENABLED_TRUE = 1;
    const ENABLED_INVALID = -1;
    const PORTSTATE_OFF = 0;
    const PORTSTATE_ON = 1;
    const PORTSTATE_RUN = 2;
    const PORTSTATE_INVALID = -1;
    const BAUDRATE_INVALID = Y_INVALID_UNSIGNED;

    /**
     * Returns the logical name of the Yocto-hub port, which is always the serial number of the
     * connected module.
     * 
     * @return a string corresponding to the logical name of the Yocto-hub port, which is always the
     * serial number of the
     *         connected module
     * 
     * On failure, throws an exception or returns Y_LOGICALNAME_INVALID.
     */
    public function get_logicalName()
    {   $json_val = $this->_getAttr("logicalName");
        return (is_null($json_val) ? Y_LOGICALNAME_INVALID : $json_val);
    }

    /**
     * It is not possible to configure the logical name of a Yocto-hub port. The logical
     * name is automatically set to the serial number of the connected module.
     * 
     * @param newval : a string
     * 
     * @return YAPI_SUCCESS if the call succeeds.
     * 
     * On failure, throws an exception or returns a negative error code.
     */
    public function set_logicalName($newval)
    {
        $rest_val = $newval;
        return $this->_setAttr("logicalName",$rest_val);
    }

    /**
     * Returns the current value of the Yocto-hub port (no more than 6 characters).
     * 
     * @return a string corresponding to the current value of the Yocto-hub port (no more than 6 characters)
     * 
     * On failure, throws an exception or returns Y_ADVERTISEDVALUE_INVALID.
     */
    public function get_advertisedValue()
    {   $json_val = $this->_getAttr("advertisedValue");
        return (is_null($json_val) ? Y_ADVERTISEDVALUE_INVALID : $json_val);
    }

    /**
     * Returns true if the Yocto-hub port is powered, false otherwise.
     * 
     * @return either Y_ENABLED_FALSE or Y_ENABLED_TRUE, according to true if the Yocto-hub port is
     * powered, false otherwise
     * 
     * On failure, throws an exception or returns Y_ENABLED_INVALID.
     */
    public function get_enabled()
    {   $json_val = $this->_getAttr("enabled");
        return (is_null($json_val) ? Y_ENABLED_INVALID : intval($json_val));
    }

    /**
     * Changes the activation of the Yocto-hub port. If the port is enabled, the
     * *      connected module will be powered. Otherwise, port power will be shut down.
     * 
     * @param newval : either Y_ENABLED_FALSE or Y_ENABLED_TRUE, according to the activation of the Yocto-hub port
     * 
     * @return YAPI_SUCCESS if the call succeeds.
     * 
     * On failure, throws an exception or returns a negative error code.
     */
    public function set_enabled($newval)
    {
        $rest_val = strval($newval);
        return $this->_setAttr("enabled",$rest_val);
    }

    /**
     * Returns the current state of the Yocto-hub port.
     * 
     * @return a value among Y_PORTSTATE_OFF, Y_PORTSTATE_ON and Y_PORTSTATE_RUN corresponding to the
     * current state of the Yocto-hub port
     * 
     * On failure, throws an exception or returns Y_PORTSTATE_INVALID.
     */
    public function get_portState()
    {   $json_val = $this->_getAttr("portState");
        return (is_null($json_val) ? Y_PORTSTATE_INVALID : intval($json_val));
    }

    /**
     * Returns the current baud rate used by this Yocto-hub port, in kbps.
     * The default value is 1000 kbps, but a slower rate may be used if communication
     * problems are hit.
     * 
     * @return an integer corresponding to the current baud rate used by this Yocto-hub port, in kbps
     * 
     * On failure, throws an exception or returns Y_BAUDRATE_INVALID.
     */
    public function get_baudRate()
    {   $json_val = $this->_getAttr("baudRate");
        return (is_null($json_val) ? Y_BAUDRATE_INVALID : intval($json_val));
    }

    public function logicalName()
    { return get_logicalName(); }

    public function setLogicalName($newval)
    { return set_logicalName($newval); }

    public function advertisedValue()
    { return get_advertisedValue(); }

    public function enabled()
    { return get_enabled(); }

    public function setEnabled($newval)
    { return set_enabled($newval); }

    public function portState()
    { return get_portState(); }

    public function baudRate()
    { return get_baudRate(); }

    /**
     * Continues the enumeration of Yocto-hub ports started using yFirstHubPort().
     * 
     * @return a pointer to a YHubPort object, corresponding to
     *         a Yocto-hub port currently online, or a null pointer
     *         if there are no more Yocto-hub ports to enumerate.
     */
    public function nextHubPort()
    {   $next_hwid = YAPI::getNextHardwareId($this->_className, $this->_func);
        if($next_hwid == null) return null;
        return yFindHubPort($next_hwid);
    }

    /**
     * Retrieves a Yocto-hub port for a given identifier.
     * The identifier can be specified using several formats:
     * <ul>
     * <li>FunctionLogicalName</li>
     * <li>ModuleSerialNumber.FunctionIdentifier</li>
     * <li>ModuleSerialNumber.FunctionLogicalName</li>
     * <li>ModuleLogicalName.FunctionIdentifier</li>
     * <li>ModuleLogicalName.FunctionLogicalName</li>
     * </ul>
     * 
     * This function does not require that the Yocto-hub port is online at the time
     * it is invoked. The returned object is nevertheless valid.
     * Use the method YHubPort.isOnline() to test if the Yocto-hub port is
     * indeed online at a given time. In case of ambiguity when looking for
     * a Yocto-hub port by logical name, no error is notified: the first instance
     * found is returned. The search is performed first by hardware name,
     * then by logical name.
     * 
     * @param func : a string that uniquely characterizes the Yocto-hub port
     * 
     * @return a YHubPort object allowing you to drive the Yocto-hub port.
     */
    public static function FindHubPort($str_func)
    {   $obj_func = YAPI::getFunction('HubPort', $str_func);
        if($obj_func) return $obj_func;
        return new YHubPort($str_func);
    }

    /**
     * Starts the enumeration of Yocto-hub ports currently accessible.
     * Use the method YHubPort.nextHubPort() to iterate on
     * next Yocto-hub ports.
     * 
     * @return a pointer to a YHubPort object, corresponding to
     *         the first Yocto-hub port currently online, or a null pointer
     *         if there are none.
     */
    public static function FirstHubPort()
    {   $next_hwid = YAPI::getFirstHardwareId('HubPort');
        if($next_hwid == null) return null;
        return self::FindHubPort($next_hwid);
    }

    //--- (end of YHubPort implementation)

    function __construct($str_func)
    {
        //--- (YHubPort constructor)
        parent::__construct('HubPort', $str_func);
        //--- (end of YHubPort constructor)
    }
};

//--- (HubPort functions)

/**
 * Retrieves a Yocto-hub port for a given identifier.
 * The identifier can be specified using several formats:
 * <ul>
 * <li>FunctionLogicalName</li>
 * <li>ModuleSerialNumber.FunctionIdentifier</li>
 * <li>ModuleSerialNumber.FunctionLogicalName</li>
 * <li>ModuleLogicalName.FunctionIdentifier</li>
 * <li>ModuleLogicalName.FunctionLogicalName</li>
 * </ul>
 * 
 * This function does not require that the Yocto-hub port is online at the time
 * it is invoked. The returned object is nevertheless valid.
 * Use the method YHubPort.isOnline() to test if the Yocto-hub port is
 * indeed online at a given time. In case of ambiguity when looking for
 * a Yocto-hub port by logical name, no error is notified: the first instance
 * found is returned. The search is performed first by hardware name,
 * then by logical name.
 * 
 * @param func : a string that uniquely characterizes the Yocto-hub port
 * 
 * @return a YHubPort object allowing you to drive the Yocto-hub port.
 */
function yFindHubPort($str_func)
{
    return YHubPort::FindHubPort($str_func);
}

/**
 * Starts the enumeration of Yocto-hub ports currently accessible.
 * Use the method YHubPort.nextHubPort() to iterate on
 * next Yocto-hub ports.
 * 
 * @return a pointer to a YHubPort object, corresponding to
 *         the first Yocto-hub port currently online, or a null pointer
 *         if there are none.
 */
function yFirstHubPort()
{
    return YHubPort::FirstHubPort();
}

//--- (end of HubPort functions)
?>