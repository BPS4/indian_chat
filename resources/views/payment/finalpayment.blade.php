{{$order}}<br>
{{$payment}}

  <a id="payNowButton" class="btn btn-success" href="#">
                                    Pay Now
                                </a>
                                
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

 <script>
        // Function to create order ID and initiate payment
        function createOrderAndPay() {

            $.ajax({
                url: "{{ url('create-order') }}/{{ $order }}",
                type: "GET",
                success: function(data) {
                    console.log(data);
                    // return false
                    var options = {
                        "key": "rzp_test_lu4pLKLi15i2yB",
                        "amount": data.amount,
                        "currency": data.currency,
                        "name": "Weekend Trip",
                        "description": "",
                        "image": "https://admin.myweekendtrip.in/public/frontend/assets/img/favicon.png",
                        "order_id": data.order_id,
                        "callback_url": "{{ url('booking-success') }}/{{ $order }}",
                        "prefill": {
                            "name": data.customer.name,
                            "email": data.customer.email,
                            "contact": data.customer.contact
                        },
                        "notes": {
                            "address": "Razorpay Corporate Office"
                        },
                        "theme": {
                            "color": "#3399cc"
                        }
                    };
                    var rzp1 = new Razorpay(options);
                    rzp1.open();
                    // e.preventDefault();
                },
                // Error handling
                error: function(error) {
                    console.log(`Error ${error}`);
                }
            });
        }

        document.getElementById('payNowButton').addEventListener('click', function(event) {
            event.preventDefault();
            createOrderAndPay();
        });
    </script>
