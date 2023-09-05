<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Subscription;
use DB;
use Illuminate\Support\Facades\Hash;

class SubscriptionController extends Controller
{
    //
    public function changeResumeType(Request $request)
    {
        $id = $request->session()->get('yak_user_id');

        $resume = DB::table('resumes')->where('id',$request->resume_id)->update(['type' => 'chronological']);

        $page = 11;
        if($id != '') {
            $user = DB::table('users')->where('id',$id)->first();
            $page = $user->page;
        }
        return response()->json([
            'success' => true,
            'status' => 1,
            'resume_id' => $request->resume_id,
            'page' => $page
        ]);
    }

    public function subscriptionList(Request $request)
    {
        $subscriptionData = Subscription::get()->toArray();;
        $count = [];
        if(!empty($subscriptionData)) {
            foreach ($subscriptionData AS $keys => $value) {
                $subscription_id = $value['id'];
                $subscriptionOption = DB::select("SELECT id, subscription_id, content FROM subscription_option WHERE subscription_id = $subscription_id");
                $subscriptionData[$keys]['subscription_option'] = $subscriptionOption;

                $count[] = count($subscriptionOption);
                $subscriptionData[$keys]['option_count'] = count($subscriptionOption);
            }
        }
        
        return response()->json([
            'success' => true,
            'status' => 1,
            'data' => $subscriptionData,
            'count' => max($count)
        ]);
    }

    public function getSubscriptionData(Request $request)
    {
        $subscription = Subscription::where('id','=',$request->subscription_id)->get()->first();
        if(!empty($subscription)) {
            return response()->json([
                'success' => true,
                'status' => 1,
                'data' => $subscription
            ]);
        }
        else {
            return response()->json([
                'success' => false,
                'status' => 0,
                'data' => ''
            ]);
        }
        
    }
    
    public function addSubscription(Request $request)
    {
        $id = ($request->session()->get('yak_user_id')) ? $request->session()->get('yak_user_id') : 0;
        
        if($id != 0) {
            $user = DB::table('users')->where('id',$id)->first();
            $email = $user->email;
            $name = $user->first_name.''.$user->last_name;
            $page = $user->page;
            $customer_token = $user->customer_token;

            $curl = new \Stripe\HttpClient\CurlClient();
            $curl->setEnablePersistentConnections(false);
            \Stripe\ApiRequestor::setHttpClient($curl);

            \Stripe\Stripe::setApiKey(config('app.stripe_secret'));
            if($customer_token == '') {
                // Create Customer in Stripe
                try{            
                    $customer = \Stripe\Customer::create(
                        [
                            "description" => '',
                            "email" => $email,
                            "name" => $name,
                        ]
                    );
                    $customer_token = $customer->id;
                    $message = '';

                    DB::table('users')->where('id',$id)->update(['customer_token' => $customer_token, 'updated_at' => date('Y-m-d')]);
                } 
                catch(\Stripe\Exception\CardException $e) {            
                    $message = self::getErrorMessage($e);
                } 
                catch (\Stripe\Exception\RateLimitException $e) {
                    $message = self::getErrorMessage($e);
                } 
                catch (\Stripe\Exception\InvalidRequestException $e) {
                    $message = self::getErrorMessage($e);
                } 
                catch (\Stripe\Exception\AuthenticationException $e) {
                    $message = self::getErrorMessage($e);
                } 
                catch (\Stripe\Exception\ApiConnectionException $e) {
                    $message = self::getErrorMessage($e);
                } 
                catch (\Stripe\Exception\ApiErrorException $e) {
                    $message = self::getErrorMessage($e);
                } 
                catch (Exception $e) {
                    $message = self::getErrorMessage($e);
                }
                // End
            }
            // Create Card in Stripe
            $date = explode('/',$request->expiration);
            try
            {
                $card = \Stripe\Customer::allSources(
                    $customer_token,
                    ['object' => 'card']
                );
                $card_token = (!empty($card->data)) ? $card->data[0]->id : '';
                
                if($card_token == '') {
                    $customers = \Stripe\Customer::retrieve($customer_token);
                    $token = \Stripe\Token::create([
                        "card" => [
                            "name"	=> $request->card_name,
                            "number" => $request->card_number,
                            "exp_month" => $date[0],
                            "exp_year" => $date[1],
                            "cvc" => $request->cvc,
                        ]
                    ]);
                    $card_stripe_token = $token->id;
                    $card_token = $token->card->id;
                    
                    /*add card in customer */
                    \Stripe\Customer::createSource(
                        $customer_token,
                        [
                            'source' => $card_stripe_token,
                        ]
                    );
                }

                if($card_token != '') {
                    $amount = $request->price * 100;
                    $msg = ($request->billing_plan == 1) ? 'Purchased monthly subscription plan' : 'Purchased annual subscription plan';
                    // Create charge in stripe
                    $charge = \Stripe\Charge::create(
                        [
                            "amount" => $amount,
                            "currency" => 'usd',
                            "customer" => $customer_token,
                            "source" => $card_token,
                            "description" => $msg,
                        ]
                    );
                }
                // End
                $message = '';  
            }
            catch(\Stripe\Exception\CardException $e) {            
                $message = self::getErrorMessage($e);
            } 
            catch (\Stripe\Exception\RateLimitException $e) {
                $message = self::getErrorMessage($e);
            } 
            catch (\Stripe\Exception\InvalidRequestException $e) {
                $message = self::getErrorMessage($e);
            } 
            catch (\Stripe\Exception\AuthenticationException $e) {
                $message = self::getErrorMessage($e);
            } 
            catch (\Stripe\Exception\ApiConnectionException $e) {
                $message = self::getErrorMessage($e);
            } 
            catch (\Stripe\Exception\ApiErrorException $e) {
                $message = self::getErrorMessage($e);
            } 
            catch (Exception $e) {
                $message = self::getErrorMessage($e);
            }
             // End

            if($message == '') {
                $start_date = date('Y-m-d');
                if($request->billing_plan == 1) {
                    $subscription_frequency = 'monthly';
                    $end_date = date('Y-m-d', strtotime("+1 month", strtotime($start_date)));
                }
                else {
                    $subscription_frequency = 'annual';
                    $end_date = date('Y-m-d', strtotime("+1 year", strtotime($start_date)));
                }
                
                $subscriptionID = DB::table('subscription_history')->insertGetId(['user_id' => $id, 'subscription_id' => $request->subscription_id, 'subscription_frequency' => $subscription_frequency, 'start_date' => $start_date, 'end_date' => $end_date, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);

            
                DB::table('users')->where('id',$id)->update(['subscription_id' => $request->subscription_id, 'subscription_frequency' => $subscription_frequency, 'renewal_date' => date('Y-m-d', strtotime('+1 day', strtotime($end_date)))]);

                return response()->json([
                    'success' => true,
                    'status' => 1,
                    'subscription_id' => $subscriptionID,
                    'page' => $page
                ]);
            } 
            else {
                return response()->json([
                    'success' => false,
                    'status' => 0,
                    'msg' => $message
                ]);
            }
        }
        else {
            return response()->json([
                'success' => false,
                'status' => 0,
                'msg' => 'User is not found'
            ]);
        }
        
    }

    /**
     * To get error message
     */
    public static function getErrorMessage($e) {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        return $err['message'];
    }
}
