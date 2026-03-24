# Bugfix Requirements Document

## Introduction

The hero section on the homepage is completely invisible to users. The section uses an Alpine.js component (`heroSlider`) registered via `Alpine.data('heroSlider', ...)` inside an inline `<script>` tag placed within `@section('content')` in `home.blade.php`. Because Alpine.js is loaded through Vite (which defers script execution), Alpine initializes before the inline `alpine:init` listener in the content section is registered, causing the `heroSlider` component to be unknown at initialization time. As a result, all slides are hidden by their `x-show="$root.activeSlide === N"` directives and the entire hero section renders as invisible content.

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN the homepage is loaded and Alpine.js initializes via the Vite bundle THEN the system fails to register the `heroSlider` Alpine component because the inline `alpine:init` listener in `home.blade.php` is attached after Alpine has already fired its `alpine:init` event

1.2 WHEN the `heroSlider` component is not registered at Alpine initialization time THEN the system renders the `<section x-data="heroSlider">` element with an unknown component, causing all child elements using `x-show="$root.activeSlide === N"` to evaluate against an undefined data context and remain hidden

1.3 WHEN the hero section is invisible due to failed Alpine component registration THEN the system displays a blank area at the top of the homepage instead of the hero slider content

### Expected Behavior (Correct)

2.1 WHEN the homepage is loaded THEN the system SHALL register the `heroSlider` Alpine component before Alpine initializes, ensuring `x-data="heroSlider"` resolves correctly

2.2 WHEN the `heroSlider` component is registered before Alpine initialization THEN the system SHALL render the first slide as visible (`activeSlide === 1`) and display the hero section content to the user

2.3 WHEN the hero section renders correctly THEN the system SHALL initialize autoplay (if more than one slide exists) and respond to user interactions such as navigation arrows and pagination dots

### Unchanged Behavior (Regression Prevention)

3.1 WHEN the homepage is loaded with multiple active hero slides THEN the system SHALL CONTINUE TO cycle through slides automatically at the configured autoplay speed

3.2 WHEN the user hovers over the hero section THEN the system SHALL CONTINUE TO pause autoplay and resume it on mouse leave

3.3 WHEN the user clicks navigation arrows or pagination dots THEN the system SHALL CONTINUE TO navigate between slides and reset the autoplay timer

3.4 WHEN no active hero slides exist in the database THEN the system SHALL CONTINUE TO display the default fallback hero content

3.5 WHEN the homepage is loaded with a single active slide THEN the system SHALL CONTINUE TO display that slide without showing navigation arrows or pagination dots

---

## Bug Condition

```pascal
FUNCTION isBugCondition(X)
  INPUT: X of type PageLoadContext
  OUTPUT: boolean

  // Bug triggers when Alpine loads via deferred Vite bundle
  // and the heroSlider registration script is inline in content
  RETURN X.alpineLoadedViaVite = true
     AND X.heroSliderScriptPosition = "inline-in-content-section"
END FUNCTION
```

```pascal
// Property: Fix Checking
FOR ALL X WHERE isBugCondition(X) DO
  result ← renderHomepage'(X)
  ASSERT heroSectionVisible(result) = true
     AND activeSlide(result) = 1
END FOR

// Property: Preservation Checking
FOR ALL X WHERE NOT isBugCondition(X) DO
  ASSERT renderHomepage(X) = renderHomepage'(X)
END FOR
```
