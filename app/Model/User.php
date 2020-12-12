<?php

namespace Matcha\Model;

class User
{
    private $_usr_id;
    private $_usr_login;
    private $_usr_pwd;
    private $_usr_email;
    private $_usr_name;
    private $_usr_dob;
    private $_usr_bio;
    private $_usr_ppic;
    private $_usr_orientation;
    private $_usr_gender;
    private $_usr_tags;
    private $_usr_rating;
    private $_usr_social;
    private $_usr_idsocial;
    private $_usr_geoconsent;
    private $_usr_lat;
    private $_usr_long;
    private $_usr_city;
    private $_usr_country;
    private $_usr_lik_sendmail;
    private $_usr_msg_sendmail;
    private $_usr_vst_sendmail;
    private $_usr_token;
    private $_usr_confirmed;
    private $_usr_active;
    private $_usr_lastseen;
    private $_usr_regdate;

    public function __construct($data)
    {
        if (!empty($data))
            $this->hydrate($data);
    }

    public function hydrate($data)
    {
        foreach ($data as $key => $value) {
            $method = 'set_' . $key;
            if (method_exists($this, $method))
                $this->$method($value);
        }
    }

    /**
     * Getters
     */

    public function get_usr_id()
    {
        return ($this->_usr_id);
    }

    public function get_usr_login()
    {
        return ($this->_usr_login);
    }

    public function get_usr_pwd()
    {
        return ($this->_usr_pwd);
    }

    public function get_usr_email()
    {
        return ($this->_usr_email);
    }

    public function get_usr_name()
    {
        return ($this->_usr_name);
    }

    public function get_usr_dob()
    {
        return ($this->_usr_dob);
    }

    public function get_usr_bio()
    {
        return ($this->_usr_bio);
    }

    public function get_usr_ppic()
    {
        return ($this->_usr_ppic);
    }

    public function get_usr_orientation()
    {
        return ($this->_usr_orientation);
    }

    public function get_usr_gender()
    {
        return ($this->_usr_gender);
    }

    public function get_usr_tags()
    {
        return ($this->_usr_tags);
    }

    public function get_usr_rating()
    {
        return ($this->_usr_rating);
    }

    public function get_usr_social()
    {
        return ($this->_usr_social);
    }

    public function get_usr_idsocial()
    {
        return ($this->_usr_idsocial);
    }

    public function get_usr_geoconsent()
    {
        return ($this->_usr_geoconsent);
    }

    public function get_usr_lat()
    {
        return ($this->_usr_lat);
    }

    public function get_usr_long()
    {
        return ($this->_usr_long);
    }

    public function get_usr_city()
    {
        return ($this->_usr_city);
    }

    public function get_usr_country()
    {
        return ($this->_usr_country);
    }

    public function get_usr_lik_sendmail()
    {
        return ($this->_usr_lik_sendmail);
    }

    public function get_usr_msg_sendmail()
    {
        return ($this->_usr_msg_sendmail);
    }

    public function get_usr_vst_sendmail()
    {
        return ($this->_usr_vst_sendmail);
    }

    public function get_usr_token()
    {
        return ($this->_usr_token);
    }

    public function get_usr_confirmed()
    {
        return ($this->_usr_confirmed);
    }

    public function get_usr_active()
    {
        return ($this->_usr_active);
    }

    public function get_usr_lastseen()
    {
        return ($this->_usr_lastseen);
    }

    public function get_usr_regdate()
    {
        return ($this->_usr_regdate);
    }

    /**
     * Setters
     */

    public function set_usr_id($_usr_id)
    {
        $this->_usr_id = (int)$_usr_id;
    }

    public function set_usr_login($_usr_login)
    {
        if (preg_match("/^[a-z\d_-]{3,20}$/i", $_usr_login))
            $this->_usr_login = $_usr_login;
    }

    public function set_usr_pwd($_usr_pwd)
    {
        if (is_string($_usr_pwd))
            $this->_usr_pwd = $_usr_pwd;
    }

    public function set_usr_email($_usr_email)
    {
        if (filter_var($_usr_email, FILTER_VALIDATE_EMAIL))
            $this->_usr_email = $_usr_email;
    }

    public function set_usr_name($_usr_name)
    {
        if (is_string($_usr_name))
            $this->_usr_name = $_usr_name;
    }

    public function set_usr_dob($_usr_dob)
    {
        if (is_string($_usr_dob))
            $this->_usr_dob = $_usr_dob;
    }

    public function set_usr_bio($_usr_bio)
    {
        if (is_string($_usr_bio))
            $this->_usr_bio = $_usr_bio;
    }

    public function set_usr_ppic($_usr_ppic)
    {
        if (is_string($_usr_ppic))
            $this->_usr_ppic = $_usr_ppic;
    }

    public function set_usr_orientation($_usr_orientation)
    {
        if ($_usr_orientation == 0 || $_usr_orientation == 1 || $_usr_orientation == 2)
            $this->_usr_orientation = (int)$_usr_orientation;
    }

    public function set_usr_gender($_usr_gender)
    {
        if ($_usr_gender == 0 || $_usr_gender == 1 || $_usr_gender == 2 || $_usr_gender == 3)
            $this->_usr_gender = (int)$_usr_gender;
    }

    public function set_usr_tags($_usr_tags)
    {
        if (is_string($_usr_tags))
            $this->_usr_tags = $_usr_tags;
    }

    public function set_usr_rating($_usr_rating)
    {
        // condition
        $this->_usr_rating = (int)$_usr_rating;
    }

    public function set_usr_social($_usr_social)
    {
        // condition
        $this->_usr_social = (int)$_usr_social;
    }

    public function set_usr_idsocial($_usr_idsocial)
    {
        $this->_usr_idsocial = $_usr_idsocial;
    }

    public function set_usr_geoconsent($_usr_geoconsent)
    {
        // condition
        $this->_usr_geoconsent = (int)$_usr_geoconsent;
    }

    public function set_usr_lat($_usr_lat)
    {
        // condition
        $this->_usr_lat = $_usr_lat;
    }

    public function set_usr_long($_usr_long)
    {
        // condition
        $this->_usr_long = $_usr_long;
    }

    public function set_usr_city($_usr_city)
    {
        if (is_string($_usr_city))
            $this->_usr_city = $_usr_city;
    }

    public function set_usr_country($_usr_country)
    {
        if (is_string($_usr_country))
            $this->_usr_country = $_usr_country;
    }

    public function set_usr_lik_sendmail($_usr_lik_sendmail)
    {
        if ($_usr_lik_sendmail == 0 || $_usr_lik_sendmail == 1)
            $this->_usr_lik_sendmail = (int)$_usr_lik_sendmail;
    }

    public function set_usr_msg_sendmail($_usr_msg_sendmail)
    {
        if ($_usr_msg_sendmail == 0 || $_usr_msg_sendmail == 1)
            $this->_usr_msg_sendmail = (int)$_usr_msg_sendmail;
    }

    public function set_usr_vst_sendmail($_usr_vst_sendmail)
    {
        if ($_usr_vst_sendmail == 0 || $_usr_vst_sendmail == 1)
            $this->_usr_vst_sendmail = (int)$_usr_vst_sendmail;
    }

    public function set_usr_token($_usr_token)
    {
        if (is_string($_usr_token) || is_null($_usr_token))
            $this->_usr_token = $_usr_token;
    }

    public function set_usr_confirmed($_usr_confirmed)
    {
        if ($_usr_confirmed == 0 || $_usr_confirmed == 1)
            $this->_usr_confirmed = (int)$_usr_confirmed;
    }

    public function set_usr_active($_usr_active)
    {
        if ($_usr_active == 0 || $_usr_active == 1)
            $this->_usr_active = (int)$_usr_active;
    }

    public function set_usr_lastseen($_usr_lastseen)
    {
        // condition
        $this->_usr_lastseen = $_usr_lastseen;
    }

    public function set_usr_regdate($_usr_regdate)
    {
        if (is_string($_usr_regdate))
            $this->_usr_regdate = $_usr_regdate;
    }
}
