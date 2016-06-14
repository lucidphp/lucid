# PSR7 wrapper for lucid/mux


- will take a PSR7 `ServerRequest` and dispatch it. 
- will apply a mapped request object to a match context. 

## Synopsis

This package simple introduces a class extending the `Lucid\Mux\Router` class by a dispatch method capable of handling PSR7 server requests. The `Lucid\Mux\Router` class is already capable of handling such requests, however, one needs to prepare the PSR7 request before passing it to the router dispatch method. This is perfectly valid, since it let's you plenty of choices of how to deal with outgoing matches and return values. The PSR7 wrapper however expects an `\Psr\Http\Message\ResponseInterface` implementation to be returned. Also it is kind enough to pass a mapped* request object to the arguments of a resolved routing handler. 

##TODO

more obvious reasons

--
####Notes####
--
* The request object will be attached in the match context, thus being available for any parameter parsers. The caveat is, that you can't specify a parameter named `request` in your routing definition.
