<?php
/**
 * @var DaveJamesMiller\RouteBrowser\RoutePresenter $route
 */
?>

<div class="row mb-3">
    <div class="col-sm-1">
        <h5>Route</h5>
    </div>
    <div class="col-sm-11">
        <div class="row">
            <div class="col-sm-1">
                <h6>Name</h6>
            </div>
            <div class="col-sm-11">
                @if ($name = $route->name())
                    {{ $name }}
                @else
                    <span class="text-muted">Unnamed route</span>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-sm-1">
                <h6>Type</h6>
            </div>
            <div class="col-sm-11">
                {{ $route->type() }}
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-sm-1">
        <h5>Request</h5>
    </div>
    <div class="col-sm-11">
        <div class="row">
            <div class="col-sm-1">
                <h6>Methods</h6>
            </div>
            <div class="col-sm-11">
                {{ $route->allMethods() }}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-1">
                <h6>Scheme</h6>
            </div>
            <div class="col-sm-11">
                @if ($scheme = $route->scheme())
                    {{ $scheme }}
                @else
                    <span class="text-muted">Any</span>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-sm-1">
                <h6>Domain</h6>
            </div>
            <div class="col-sm-11">
                @if ($domain = $route->domain())
                    {{ $domain }}
                @else
                    <span class="text-muted">Any</span>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-sm-1">
                <h6>Path</h6>
            </div>
            <div class="col-sm-11">
                {{ $route->path() }}
                @if ($link = $route->link())
                    (<a href="{{ $link }}" target="_blank">Visit</a>)
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-1">
        <h5>Parameters</h5>
    </div>
    <div class="col-sm-11">
        <h6>Patterns</h6>
        <ul class="list-unstyled">
            @forelse ($route->parameterPatterns() as $key => $value)
                <li class="row mb-1">
                    <span class="col-sm-1">
                        {{ $key }}
                    </span>
                    <span class="col-sm-11">
                        @if ($value)
                            <code>{{ $value }}</code>
                        @else
                            Default <span class="text-muted">(match until the next separator, e.g. "/" or ".")</span>
                        @endif
                    </span>
                </li>
            @empty
                <li class="text-muted">
                    No parameters used.
                </li>
            @endforelse
        </ul>

        <h6>Matched</h6>
        <ul class="list-unstyled">
            @forelse ($route->matchedParameters() as $key => $value)
                <li class="row mb-1">
                    <span class="col-sm-1">
                        {{ $key }}
                    </span>
                    <span class="col-sm-11">
                        <code>{{ var_export($value, true) }}</code>
                    </span>
                </li>
            @empty
                <li class="text-muted">
                    @if (request('uri'))
                        No parameters matched.
                    @else
                        Enter a URL above to match parameters.
                    @endif
                </li>
            @endforelse
        </ul>

        <h6>Defaults</h6>
        <ul class="list-unstyled">
            @forelse ($route->defaultParameters() as $key => $value)
                <li class="row mb-1">
                    <span class="col-sm-1">
                        {{ $key }}
                    </span>
                    <span class="col-sm-11">
                        <code>{{ var_export($value, true) }}</code>
                    </span>
                </li>
            @empty
                <li class="text-muted">
                    No default values set.
                </li>
            @endforelse
        </ul>
    </div>
</div>

<?php $action = $route->action() ?>
<div class="row mb-3">
    <div class="col-sm-1">
        <h5>Action</h5>
    </div>
    <div class="col-sm-11">
        @if ($class = $action->class())
            <code>{{ $class }}<span class="text-muted">::</span>{{ $action->method() }}</code>
        @else
            <code>{{ $action->method() }}</code>
        @endif

        <small class="text-muted d-block mb-1">
            <strong style="font-weight: 600;">Source:</strong>
            @if ($source = $action->source())
                @if ($code = $action->code())
                    <a href="#" data-toggle="modal" data-target="#source-code" data-code="{{ $code }}">{{ $source }}</a>
                @else
                    {{ $source }}
                @endif
            @else
                <span class="text-danger">Not found</span>
            @endif
        </small>
    </div>
</div>

<div class="row">
    <div class="col-sm-1">
        <h5>Middleware</h5>
    </div>
    <div class="col-sm-11">
        <h6>Handlers</h6>
        <ul class="list-unstyled">
            @forelse ($route->middleware() as $middleware)
                <?php $handler = $middleware->handler ?>
                <li>
                    @if ($class = $handler->class())
                        <code>
                            @if ($middleware->parameters)
                                {{ $handler->class() }}<span class="text-muted">::</span>handle<span class="text-muted">($request, $next,</span>
                                {{ $middleware->parameters }}<span class="text-muted">)</span>
                            @else
                                {{ $handler->class() }}<span class="text-muted">::</span>handle<span class="text-muted">($request, $next)</span>
                            @endif
                        </code>
                    @else
                        {{ $handler->summary() }}
                    @endif

                    <small class="text-muted d-block mb-1">
                        <strong style="font-weight: 600;">Added in:</strong>
                        <?php if (!is_string($middleware->addedIn)) { var_dump($middleware->addedIn);exit; } ?>
                        {{ $middleware->addedIn ?: 'Unknown' }}
                        @if ($middleware->original)
                            &middot;
                            <strong style="font-weight: 600;">Original:</strong>
                            {{ $middleware->original }}
                        @endif
                        @if ($source = $handler->source())
                            &middot;
                            <strong style="font-weight: 600;">Source:</strong>
                            @if ($code = $handler->code())
                                <a href="#" data-toggle="modal" data-target="#source-code" data-code="{{ $code }}">{{ $source }}</a>
                            @else
                                {{ $source }}
                            @endif
                        @endif
                    </small>
                </li>
            @empty
                <li class="text-muted">(None)</li>
            @endforelse
        </ul>

        <h6>Terminators</h6>
        <ul class="list-unstyled">
            @forelse ($route->middleware()->where('terminates', true) as $middleware)
                <?php $terminator = $middleware->terminator ?>
                <li>
                    <code>{{ $terminator->class() }}<span class="text-muted">::</span>terminate<span class="text-muted">($request, $next</span>)</code>

                    @if ($source = $terminator->source())
                        {{-- No need to repeat Added in or Original --}}
                        <small class="text-muted d-block mb-1">
                            <strong style="font-weight: 600;">Source:</strong>
                            @if ($code = $terminator->code())
                                <a href="#" data-toggle="modal" data-target="#source-code" data-code="{{ $code }}">{{ $source }}</a>
                            @else
                                {{ $source }}
                            @endif
                        </small>
                    @endif
                </li>
            @empty
                <li class="text-muted">(None)</li>
            @endforelse
        </ul>
    </div>
</div>
