<?php
/**
 *
 * @author Selim Fraoua <sfraoua@gmail.com>
 */

namespace Tanna\UserBundle\Controller;


use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Tanna\UserBundle\Model\UserInterface;

class FacebookController extends Controller
{

    public function fallbackAction(){
        $fb = new Facebook([
            'app_id' => $this->getParameter('tanna_user.facebook.app_id'),
            'app_secret' => $this->getParameter('tanna_user.facebook.app_secret'),
            'default_graph_version' => 'v2.2',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch(FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (! isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();

        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId((string) $this->getParameter('tanna_user.facebook.app_id'));

        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if (! $accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
                exit;
            }
        }

        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get('/me?fields=id,email,first_name,last_name,gender,locale,timezone,location,birthday', $accessToken);
        } catch(FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $facebookUser = $response->getGraphUser();

        $om = $this->get('tanna_user.doctrine.om');
        $user = $om->getRepository($this->getParameter('tanna_user.user.class'))->findOneByEmail($facebookUser['email']);

        //register user
        if($user === null){
            $response = $this->registerUser($facebookUser);
            if($response instanceof UserInterface){
                //login user and redirect him
                return $this->loginAndRedirect($response);
            } else{
                //@todo redirect and show errors
                die('show errors');
            }

        }else{
                return $this->loginAndRedirect($user);
        }

        // User is logged in with a long-lived access token.
        // You can redirect them to a members-only page.
        //header('Location: https://example.com/members.php');
    }


    private function registerUser($facebookUser)
    {
        $userManager = $this->get('tanna_user.user.manager');
        $user = $userManager->createUser();
        $user->setFacebookId($facebookUser['id']);
        $user->setGivenName($facebookUser['first_name']);
        $user->setFamilyName($facebookUser['last_name']);
        $user->setGender(($facebookUser['gender']=='male')?'m':'f');
        $user->setBirthday($facebookUser['birthday']);
        $user->setEmail($facebookUser['email']);
        $user->setUsername($facebookUser['email']);
        $user->setPlainPassword(rand(10000, 99999));
        $user->setRandomPassword(true);

        $validator = $this->get('validator');
        $errors = $validator->validate($user, null, array('registration'));
        if(count($errors) > 0){
            return $errors;
        }

        $userManager->updateUser($user);
        return $user;
    }

    private function loginAndRedirect($user)
    {
        $token = new UsernamePasswordToken($user, null, 'registration', $user->getRoles());
        $providers = array('main');

//        $authenticationManager = new AuthenticationProviderManager($providers);
//
//        $authenticatedToken = $authenticationManager
//            ->authenticate($token);

        $this->get('security.token_storage')->setToken($token);

        return $this->redirect($this->generateUrl($this->getParameter('tanna_user.user.registration.redirection')));

    }

}