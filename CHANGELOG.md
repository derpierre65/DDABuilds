# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.6] - 2023-01-21
### Added
- Map will scroll with the page for a better tower placement.

### Changed
- Compressed all images and saved up to 70% filesize, which also reduces the loading speed.
- Optimize thumbnail generation performance.

### Fixed
- Translation keys for new towers fixed.
- Fixed tower size for specific towers.
- The image for placing new Series-EVA Towers have now the correct image and will not be replaced after start dragging.
- Fixed search filters in build list.

## [2.0.5] - 2023-01-20
### Added
- Added new Maps The Bazaar, The Lost Metropolis and Yuletide Village.
- Added new hero Summoner.
- Added new Huntress, Squire and Series-EVA Towers.

### Removed
- Removed old Huntress, Squire and Series-EVA Towers (automatically removed in old builds).

## [2.0.4] - 2023-01-19
### Added
- Tooltip for Towers.
- Defense Units in Tower Tooltip.
- Rifted, Hardcore and AFK Able filter for builds.

### Changed
- Updated all defense units for old towers.

### Fixed
- Input Text for search is now white in dark mode.

## [2.0.3] - 2021-08-12
### Fixed
- Pagination do not used the selected filters. 

## [2.0.2] - 2021-08-12
### Added
- Multiple Filter for Game Modes and Difficulties.
### Fixed
- Tower moving with the mouse movement cause sometime "invalid field errors".
- Tower moving in saved builds.

## 2.0.1 - 2021-05-21
### Fixed
- Incorrect translation in the english localization.
- The pagination was displayed wrong with a high page count.

## [2.0.0] - 2021-05-15
### Added
- Checkbox for the rifted mode.
- The first build wave can be renamed.
- GitHub Repository link to footer.
- Basic german localization (Beta).
- Author, map, game mode and difficulty filter links in table view mode.
- Multiple map selection in build list filter.
- Reset option in build list, to reset all filters.
- Difficulty colors in build add view.
- Comments are visible in editor mode.
- Rifted, AFK Able, Hardcore tags to build list and view.

### Changed
- Updated design view for the build view.
- Updated design (changed Bootstrap version to 4.x).
- Updated medium color for a better readability in light mode.
- Updated the require attributes view.
- Dark Mode for input fields and text editor.

### Fixed
- Towers cant be placed anymore out of the map.
- New towers from disabled heros are no more visible.
- Fixed a memory leak in build edit mode.

## [1.2.2] - 2020-09-22
### Changed
- New design for the create build page.

### Fixed
- Defense Units for Lava Mines on Difficulty Insane or higher.
- Footer Margin was incorrect in Dark Mode.

## [1.2.1] - 2020-08-20
### Added
- Added new hero Series EV-A.
- Added colors to difficulty text.

## [1.2.0] - 2020-05-25
### Added
- Liked build list.
- Delete build button.
- Disable towers where not enough defense units are available.
- Report feature/bug page.
- Favorite builds.
- Dark mode (beta).

### Changed
- Marked active menu items.
- Changed the build grid list design.
- The pagination are centered.
- Moved changelog link to footer.
- Reduced mouse wheel rotation speed.

### Fixed
- Hardcore/AFK Able checkboxes have not saved the correctly value.
- Hide empty "Exp Per run" and "Time Per Run".
- Disable tower rotation with mouse wheel for auras/traps.
- Wrong save for required stats in the build.

### Removed
- Notification link in user menu.

## [1.1.2] - 2020-05-25
### Added
- Filter for game mode in build list.
- Table header sort indicator (arrow up/down for asc/desc).

### Fixed
- Scroll to top button in create map view was missing.

## [1.1.1] - 2020-05-24
### Changed
- Replaced the new notification notice with a bell on top right.

### Fixed
- Defense Unit calculation on first page view for builds with multiple wave tabs.

## [1.1.0] - 2020-05-23
### Added
- A text was added if no notifications are available.
- Towers can now be rotated with the mouse wheel (shift for slower and ctrl for faster rotation).

### Changed
- Optimized the page loading speed.
- Images in the description box have a maximum width.
- The description box is hidden if none exists.
- Author's steam profile would be opened in a new tab.
- Moved delete wave button to the wave tab menu.
- Replaced input field for custom waves with prompt dialog.
- Removed the checkbox in tower disable selection.
- The newest comment is now at the top.
- The box to create a comment is now above the comments.

### Fixed
- Some xss security vulnerabilities.
- Grid view on my builds page.
- Comments tab only visible in view or edit mode.
- Page was not be reloaded after posting the second comment.
- Author with special characters in search breaks the search after initial search.