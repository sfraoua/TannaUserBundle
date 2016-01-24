<?php

/**
 *
 * @author Selim Fraoua <sfraoua@gmail.com>
 */
namespace Tanna\UserBundle;

final class TannaUserBundleEvents
{

    /**
     * The CREATE_USER_SUCCESS event occurs when the form is submitted successfully and
     * after saving the User in the database.
     *
     * This event allows you to set the response instead of using the default one.
     */
    const USER_CREATE_SUCCESS = 'tanna_user.events.user.create_success';
}