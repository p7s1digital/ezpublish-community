<?php /* #?ini charset="utf-8"?
[NavigationPart]
Part[appendixnavigationpart]=APPENDIX
Part[votingnavigationpart]=VOTING

[TopAdminMenu]
Tabs[]=appendix
Tabs[]=voting

[Topmenu_appendix]
NavigationPartIdentifier=appendixnavigationpart
Name=Appendix
Tooltip=MyVideo Appendix
URL[]
URL[default]=appendix/index
Enabled[]
Enabled[default]=true
Enabled[browse]=false
Enabled[edit]=false
Shown[]
Shown[default]=true
Shown[edit]=true
Shown[navigation]=true
# We don't show it in browse mode
Shown[browse]=false

PolicyList[]=appendix/view


[Topmenu_voting]
NavigationPartIdentifier=votingnavigationpart
Name=Voting Service
Tooltip=MyVideo Voting Service
URL[]
URL[default]=voting
Enabled[]
Enabled[default]=true
Enabled[browse]=false
Enabled[edit]=false
Shown[]
Shown[default]=false
Shown[edit]=false
Shown[navigation]=true
Shown[browse]=false

PolicyList[]=voting/*

*/
