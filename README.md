# think-log
thinkphp log drivers

install and create a user function named "think_request_trace",like:

    function think_request_trace(): string
    {
        // header
        // request body
        // and so on
        // or just do nothing,return ''
    }