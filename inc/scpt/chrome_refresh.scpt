#!/usr/bin/osascript

tell application "Chrome" to tell the active tab of its first window
    reload
end tell