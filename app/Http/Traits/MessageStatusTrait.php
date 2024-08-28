<?php

namespace App\Http\Traits;

trait MessageStatusTrait 
{
   
  # for the api 

    protected $responseMessage  =  'message';
    
    protected $responseCode     =  'code';

    # Bind the key message.
    protected $successKey  =  'success';

    # Bind the key message.
    protected $errorKey  =  'error';

    # Bind the key message.
    protected $warningKey  =  'warning';

    # Bind the key message.
    protected $messageKey  =  'message';

    # Bind the key credential not match.
    protected $credentialNotMatchKey  = 'credential_not_match';

    # Bind the key sign in successfully.
    protected $signInSuccessfullyKey  = 'sign_in_successfully';

    # Bind the key message.
    protected $userNotActiveKey  = 'this_user_not_active';

	# Bind the success message.
    protected $successStatus  =  "200";
    protected $failedStatus  =  "201";

    # Bind the warning message.
    protected $warningStatus  =  "300";

    # Bind the error message.
    protected $errorStatus  =  "0";

    # Bind the error message.
    protected $errorNotFoundStatus  =  "404";

   # Bind the error message.
    protected $errorUnauthorizedStaus   =  "401";



   






    /**
     * Display the sucess message.
     * 
     * @param message $type, 
     */
    public function addMessage($type)
    {
    	# return message.
    	return 	$type.'Added Successfully';
    }

    /**
     * Display the update message.
     * 
     * @param message $type, 
     */
    public function updateMessage($type)
    {
    	# return message.
    	return 	$type.'Updated Successfully';
    }

    /**
     * Display the delete message.
     * 
     * @param message $type, 
     */
    public function deleteMessage($type)
    {
    	# return message.
    	return 	$type.'Deleted Successfully';
    }

    /**
     * Display the error message.
     * 
     * @param
     */
    public function errorMessage()
    {
    	# return message.
    	return 	'Something went to wrong';
    }

    /**
     * Display the warning message.
     * 
     * @param 
     */
    public function warningMessage()
    {
    	# return message.
    	return 	'Added Successfully';
    }

    /**
     * Display the file not upload message.
     * 
     * @param 
     */
    public function fileNotUpload()
    {
    	# return message.
    	return 	'File can not uploaded! Please try again.';
    }

    /**
     * Display the Active message.
     * 
     * @param $type
     */
    public function activeMessage($type)
    {
    	# return message.
    	return 	$type.'Active Successfully';
    }

    /**
     * Display the InActive message.
     * 
     * @param $type
     */
    public function inActiveMessage($type)
    {
    	# return message.
    	return 	$type.'InActive Successfully';
    }

   /**
     * Display the Approve message.
     * 
     * @param $type
     */
    public function approveMessage($type)
    {
        # return message.
        return  $type.'Approve Successfully';
    } 
    public function disApproveMessage($type)
    {
        # return message.
        return  $type.'Unapproved Successfully';
    }

    /**
     * Display the Reject message.
     * 
     * @param $type
     */
    public function rejectMessage($type)
    {
        # return message.
        return  $type.'Reject Successfully';
    }

    /**
     * Display the already use cant delete message.
     * 
     * @param $type
     */
    public function alreadyUse($type)
    {
        # return message.
        return  $type.'Can Not Delete! Already Use';
    }

    /**
     * Display the credential not match message.
     * 
     * @param 
     */
    public function credentialNotMatch()
    {
        # return message.
        return  'Given Credentials Are Not In Our Record';
    } 

    /**
     * Display the sign in message.
     * 
     * @param 
     */
    public function signInMessaage()
    {
        # return message.
        return  "SignIn SuccessFully";
    } 

    /**
     * Display user not active message.
     * 
     * @param 
     */
    public function userNotAciveMessage()
    {
        # return message.
        return  "This user is not Active";
    } 

    /**
     * Display the order cancel message.
     * 
     * @param 
     */
    public function orderCancelMessage()
    {
        # return message.
        return  "Your Order Cancelled Successfully";
    } 

    /**
     * Display the wishlist delete message.
     * 
     * @param 
     */
    public function wishlistDelete()
    {
        # return message.
        return   'Wishlist Deleted Successfully';
    }    

    /**
     * Display the item add wishlist message.
     * 
     * @param
     */
    public function itemAddWishlist()
    {
        # return message.
        return   'Your Item Add Successfully to Wishlist';
    }    

    /**
     * Display the item remove message.
     * 
     * @param
     */
    public function itemRemove()
    {
        # return message.
        return   'Item Removed Successfully';
    }    

    /**
     * Display the add gift wrap message.
     * 
     * @param
     */
    public function addGiftWrap()
    {
        # return message.
        return  'Gift Wrapper Add Successfully';
    }    

    /**
     * Display the remove gift wrap message.
     * 
     * @param 
     */
    public function removeGiftWrap()
    {
        # return message.
        return 'Gift Wrapper Remove Successfully';
    } 

    /**
     * Display the coupon apply message.
     * 
     * @param 
     */
    public function couponApply()
    {
        # return message.
        return 'Coupon Code Applied Successfully';
    }   

    /**
     * Display the coupon already use message.
     * 
     * @param 
     */
    public function couponAlreadyUse()
    {
        # return message.
        return  'You have already use coupon code';
    }   

    /**
     * Display the coupon already use message.
     * 
     * @param 
     */
    public function notFound($type)
    {
        # return message.
        return  $type.'Not Found';
    }   

    /**
     * Display the order Booked message.
     * 
     * @param 
     */
    public function orderBooked()
    {
        # return message.
        return  'Your Order Booked Successfully';
    }   

	
}