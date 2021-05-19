# Changelog
All notable changes.

## [Unreleased]

### Version [1.0.0]
#### Added
* Showing keyboard shortcuts icons for select dropdown and button in category judgment.

* Options for original/flip(paired)/randomize images and sets have been moved into "experiment steps"
so that they can be controlled on a step by step basis, not just globally.
  * The way the stimuli queue is handled has to be rewritten to handle these changes. The previous way was not as solid or readable. And
  the new way is better if we want to add other stimuli types (video for example) in the future, or
  allow for more ways of generating different queues for different observers. This rewrite also fixes a problem
  where if you set "show original" to true and had two image sets where only one the first one had
  a original, the queue wouldn't know a new image set was loaded, and the original from the first
  set would still be shown alongside the second image set.

* Remember users progress if user hits the browsers "refresh" button, instead of loading a new one.

* Cookie concent.

#### Changed

#### Removed

#### Fixed
* Paired comparison now works with only two images.