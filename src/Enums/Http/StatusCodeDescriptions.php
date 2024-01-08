<?php

declare(strict_types=1);

/**
 * Utility - Collection of various PHP utility functions.
 *
 * @author    Eric Sizemore <admin@secondversion.com>
 * @version   2.0.0
 * @copyright (C) 2017 - 2024 Eric Sizemore
 * @license   The MIT License (MIT)
 *
 * Copyright (C) 2017 - 2024 Eric Sizemore <https://www.secondversion.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace Esi\Utility\Enums\Http;

/**
 * Enum of status code descriptions gathered from the MDN (Mozilla Developer Network).
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status
 *
 * Description content used from the MDN is licensed under the CC-BY-SA 2.5 license.
 * @see https://github.com/mdn/content/blob/main/LICENSE.md#license-for-all-prose-content
 * @see https://creativecommons.org/licenses/by-sa/2.5/
 *
 * @since 2.0.0
 */
enum StatusCodeDescriptions: string
{
    case Continue = 'This interim response indicates that the client should continue the request or ignore the response if the request is already finished.';
    case Switching_Protocols = 'This code is sent in response to an Upgrade request header from the client and indicates the protocol the server is switching to.';
    case Processing = '[WebDav] This code indicates that the server has received and is processing the request, but no response is available yet.';
    case Early_Hints = 'This status code is primarily intended to be used with the Link header, letting the user agent start preloading resources while the server prepares a response or preconnect to an origin from which the page will need resources.';
    case OK = 'The request succeeded. The result meaning of "success" depends on the HTTP method,';
    case Created = 'The request succeeded, and a new resource was created as a result. This is typically the response sent after POST requests, or some PUT requests.';
    case Accepted = 'The request has been received but not yet acted upon. It is noncommittal, since there is no way in HTTP to later send an asynchronous response indicating the outcome of the request. It is intended for cases where another process or server handles the request, or for batch processing.';
    case Non_Authoritative_Information = 'This response code means the returned metadata is not exactly the same as is available from the origin server, but is collected from a local or a third-party copy. This is mostly used for mirrors or backups of another resource. Except for that specific case, the 200 OK response is preferred to this status.';
    case No_Content = 'There is no content to send for this request, but the headers may be useful. The user agent may update its cached headers for this resource with the new ones.';
    case Reset_Content = 'Tells the user agent to reset the document which sent this request.';
    case Partial_Content = 'This response code is used when the Range header is sent from the client to request only part of a resource.';
    case Multi_Status = '[WebDav] Conveys information about multiple resources, for situations where multiple status codes might be appropriate.';
    case Already_Reported = '[WebDav] Used inside a <dav:propstat> response element to avoid repeatedly enumerating the internal members of multiple bindings to the same collection.';
    case IM_Used = 'The server has fulfilled a GET request for the resource, and the response is a representation of the result of one or more instance-manipulations applied to the current instance.';
    case Multiple_Choices = 'The request has more than one possible response. The user agent or user should choose one of them. (There is no standardized way of choosing one of the responses, but HTML links to the possibilities are recommended so the user can pick.)';
    case Moved_Permanently = 'The URL of the requested resource has been changed permanently. The new URL is given in the response.';
    case Found = 'This response code means that the URI of requested resource has been changed temporarily. Further changes in the URI might be made in the future. Therefore, this same URI should be used by the client in future requests.';
    case See_Other = 'The server sent this response to direct the client to get the requested resource at another URI with a GET request.';
    case Not_Modified = 'This is used for caching purposes. It tells the client that the response has not been modified, so the client can continue to use the same cached version of the response.';
    case Use_Proxy = 'Defined in a previous version of the HTTP specification to indicate that a requested response must be accessed by a proxy. It has been deprecated due to security concerns regarding in-band configuration of a proxy.';
    case Explicitly_Unused = 'This response code is no longer used; it is just reserved. It was used in a previous version of the HTTP/1.1 specification.';
    case Temporary_Redirect = 'The server sends this response to direct the client to get the requested resource at another URI with the same method that was used in the prior request. This has the same semantics as the 302 Found HTTP response code, with the exception that the user agent must not change the HTTP method used: if a POST was used in the first request, a POST must be used in the second request.';
    case Permanent_Redirect = 'This means that the resource is now permanently located at another URI, specified by the Location: HTTP Response header. This has the same semantics as the 301 Moved Permanently HTTP response code, with the exception that the user agent must not change the HTTP method used: if a POST was used in the first request, a POST must be used in the second request.';
    case Bad_Request = 'The server cannot or will not process the request due to something that is perceived to be a client error (e.g., malformed request syntax, invalid request message framing, or deceptive request routing).';
    case Unauthorized = 'Although the HTTP standard specifies "unauthorized", semantically this response means "unauthenticated". That is, the client must authenticate itself to get the requested response.';
    case Payment_Required = 'This response code is reserved for future use. The initial aim for creating this code was using it for digital payment systems, however this status code is used very rarely and no standard convention exists.';
    case Forbidden = 'The client does not have access rights to the content; that is, it is unauthorized, so the server is refusing to give the requested resource. Unlike 401 Unauthorized, the client\'s identity is known to the server.';
    case Not_Found = 'The server cannot find the requested resource. In the browser, this means the URL is not recognized. In an API, this can also mean that the endpoint is valid but the resource itself does not exist. Servers may also send this response instead of 403 Forbidden to hide the existence of a resource from an unauthorized client. This response code is probably the most well known due to its frequent occurrence on the web.';
    case Method_Not_Allowed = 'The request method is known by the server but is not supported by the target resource. For example, an API may not allow calling DELETE to remove a resource.';
    case Not_Acceptable = 'This response is sent when the web server, after performing server-driven content negotiation, doesn\'t find any content that conforms to the criteria given by the user agent.';
    case Proxy_Authentication_Required = 'This is similar to 401 Unauthorized but authentication is needed to be done by a proxy.';
    case Request_Timeout = 'This response is sent on an idle connection by some servers, even without any previous request by the client. It means that the server would like to shut down this unused connection.';
    case Conflict = 'This response is sent when a request conflicts with the current state of the server.';
    case Gone = 'This response is sent when the requested content has been permanently deleted from server, with no forwarding address. Clients are expected to remove their caches and links to the resource. The HTTP specification intends this status code to be used for "limited-time, promotional services". APIs should not feel compelled to indicate resources that have been deleted with this status code.';
    case Length_Required = 'Server rejected the request because the Content-Length header field is not defined and the server requires it.';
    case Precondition_Failed = 'The client has indicated preconditions in its headers which the server does not meet.';
    case Payload_Too_Large = 'Request entity is larger than limits defined by server. The server might close the connection or return an Retry-After header field.';
    case Request_URI_Too_Long = 'The URI requested by the client is longer than the server is willing to interpret.';
    case Unsupported_Media_Type = 'The media format of the requested data is not supported by the server, so the server is rejecting the request.';
    case Requested_Range_Not_Satisfiable = 'The range specified by the Range header field in the request cannot be fulfilled. It\'s possible that the range is outside the size of the target URI\'s data.';
    case Expectation_Failed = 'This response code means the expectation indicated by the Expect request header field cannot be met by the server.';
    case Im_A_Teapot = 'The server refuses the attempt to brew coffee with a teapot.';
    case Misdirected_Request = 'The request was directed at a server that is not able to produce a response. This can be sent by a server that is not configured to produce responses for the combination of scheme and authority that are included in the request URI.';
    case Unprocessable_Entity = '[WebDav] The request was well-formed but was unable to be followed due to semantic errors.';
    case Locked = '[WebDav] The resource that is being accessed is locked.';
    case Failed_Dependency = '[WebDav] The request failed due to failure of a previous request.';
    case Too_Early = 'Indicates that the server is unwilling to risk processing a request that might be replayed.';
    case Upgrade_Required = 'The server refuses to perform the request using the current protocol but might be willing to do so after the client upgrades to a different protocol. The server sends an Upgrade header in a 426 response to indicate the required protocol(s).';
    case Precondition_Required = 'The origin server requires the request to be conditional. This response is intended to prevent the \'lost update\' problem, where a client GETs a resource\'s state, modifies it and PUTs it back to the server, when meanwhile a third party has modified the state on the server, leading to a conflict.';
    case Too_Many_Requests = 'The user has sent too many requests in a given amount of time ("rate limiting").';
    case Request_Header_Fields_Too_Large = 'The server is unwilling to process the request because its header fields are too large. The request may be resubmitted after reducing the size of the request header fields.';
    case Unavailable_For_Legal_Reasons = 'The user agent requested a resource that cannot legally be provided, such as a web page censored by a government.';
    case Internal_Server_Error = 'The server has encountered a situation it does not know how to handle.';
    case Not_Implemented = 'The request method is not supported by the server and cannot be handled. The only methods that servers are required to support (and therefore that must not return this code) are GET and HEAD.';
    case Bad_Gateway = 'This error response means that the server, while working as a gateway to get a response needed to handle the request, got an invalid response.';
    case Service_Unavailable = 'The server is not ready to handle the request. Common causes are a server that is down for maintenance or that is overloaded.';
    case Gateway_Timeout = 'This error response is given when the server is acting as a gateway and cannot get a response in time.';
    case HTTP_Version_Not_Supported = 'The HTTP version used in the request is not supported by the server.';
    case Variant_Also_Negotiates = 'The server has an internal configuration error: the chosen variant resource is configured to engage in transparent content negotiation itself, and is therefore not a proper end point in the negotiation process.';
    case Insufficient_Storage = '[WebDav] The method could not be performed on the resource because the server is unable to store the representation needed to successfully complete the request.';
    case Loop_Detected = '[WebDav] The server detected an infinite loop while processing the request.';
    case Not_Extended = 'Further extensions to the request are required for the server to fulfill it.';
    case Network_Authentication_Required = 'Indicates that the client needs to authenticate to gain network access.';

    /**
     * Returns the value of a given case.
     *  eg: StatusCodeDescriptions::Not_Extended->getValue()
     *      // Further extensions to the request are required for the server to fulfill it.
     *
     * @return string Status Code Description
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Returns the name of a given case.
     *  eg: StatusCodeDescriptions::Not_Extended->getName() // Not_Extended
     *
     * @return string Case name
     */
    public function getName(): string
    {
        return $this->name;
    }
}
