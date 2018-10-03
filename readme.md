# GoogleStoreLocator
Proximity search of Stores based on the Google API.

The "Google Store Locator" enables a proximity search for your stores/locations on your MODX-Site. Find and filter Stores/Locations by distance and provide users to find the closest store of their current position or address. GoogleStoreLocator can be easily integrated into your website and is fully customizable by chunks and styles. Just install it via the Package-Manager and place the snippet-call in your "Store-Finder" Site-Template. Place the Placeholders for the Store-List, Map and Search-Form wherever you want them to appear.

To add a store, simply add the GoogleStoreLocator TVs to your Store-Template or define custom TVs for the address and add a new Resource as a new store. Create the Store-Resources as childs of your Stores-Search-Site or set the Parent-IDs with the "&parents" property.

# API-KEY
Since the latest version of Google-API: API-KEY(s) are required! Create a API-KEY and set it in Systemsettings: [https://console.developers.google.com/](https://console.developers.google.com/)

## System Settings
| setting | description |
| --- | --- |
| googlestorelocator.apikey_server | This extra requires an Google-Map API-Key. This Key is used for the Server-Request to get lat & lng values from a given address. You can create one here: [https://console.developers.google.com/](https://console.developers.google.com/) Activate the following librarys: Google Maps Geocoding API & Google Maps JavaScript API |
| googlestorelocator.apikey_map | This extra requires an Google-Map API-Key. This Key is used for the Map in the Frontend. You can create one here: [https://console.developers.google.com/](https://console.developers.google.com/) Activate the following librarys: Google Maps Geocoding API & Google Maps JavaScript API |

## Properties
| setting | default | description |
| --- | --- | --- |
| &parents | ID of current resource | Comma-separated list of parents to search for results. |
| &tvNameZipcode | gslZipcode | Name of the TV holding the Zip-Code of the Store. |
| &tvNameCity | gslCity | Name of the TV holding the City of the Store. |
| &tvNameStreet | gslStreet | Name of the TV holding the Street of the Store. |
| &tvNameHousenumber | gslHousenumber | Name of the TV holding the Housenumber of the Store. |
| &tvNameState | gslState | Name of the TV holding the State of the Store. |
| &tvNameCountry | gslCountry | Name of the TV holding the Country of the Store. |
| &includeTVs |  | Comma-separated list of TVs that should be included in the placeholders available to each store template. Example: "storename,time" will produce the placeholders and . |
| &tvPrefix | tv. | Prefix TV property. |
| &unit | K | Options: K = kilometers / M = miles / N = nautical |
| &defaultRadius | 20 | Default selected Radius of the search Form. |
| &where |  | Filter Stores by any placeholder like: `{"active:=":"1"}` Operators: >,>=,<,<=,=,== |
| &limit | 0 | Limits the number of stores returned. Default "0" is unlimited results. |
| &offset | 0 | An offset of resources returned by the criteria to skip. |
| &location |  | Set a address to order the stores by default. This address will be replaced by the search form. |
| &locationRadius | 20 | Radius to limit the default result when &location is set. |
| &markerImage |  | A URL to an image to be used instead of the default Google Map marker. |
| &markerImageLocation |  | A URL to an image to be used instead of the default Google Map marker for the User-Position. |
| &sortby | menuindex | Any Resource Field (excluding Template Variables) to sort by. Some common fields to sort on are publishedon, menuindex, pagetitle etc, but see the Resources documentation for all fields. |
| &sortdir | desc | Sort direction : asc or desc |
| &region |  | Prefere a region to lookup for geodata first. Google-Region-Codes are like "de" for Germany etc. |
| &totalVar | total | Name of the placeholder for storing the total number of results. |

## Template Properties
| setting | default | description |
| --- | --- | --- |
| &tplForm | gslFormTpl | Name of the Chunk to format the Search-Form. |
| &tplStore | gslStoreTpl | Name of the Chunk to format the Stores. |
| &tplMap | gslMapTpl | Name of the Chunk to format the Map. |
| &tplMapMarker | gslMapMarkerTpl | Name of the Chunk to format the Map-Marker. |
| &tplMapMarkerContent | gslMapMarkerContentTpl | Name of the Chunk to format the Map-Marker-Content. |
| &tplNoResult | gslNoResultTpl | Name of the Chunk shows when no results are found. |
| &tplMessage | gslMessageTpl | Name of the Chunk showing the message for the address search. |

## Map Properties
| setting | default | description |
| --- | --- | --- |
| &zoom | 8 | Standard zoom level when the map initializes. Option: Number between 1 - 15. |
| &latCenter | 49.14721 | Latitude on which the map will center by default |
| &lngCenter | 8.2202 | Longitude on which the map will center by default |
| &mapCSS | `height: 400px; margin: 30px 0;` | Inline CSS to style the Map-Container. Leave this empty to style this element in your own CSS-File. |
| &mapStyle |  | JSON Code to style the Map. Example: `[{"featureType": "water","stylers": [{ "color": "#80809e" }]`}] Styling-Wizard for Google-Maps: [https://mapstyle.withgoogle.com/](https://mapstyle.withgoogle.com/) |
| &autoZoomCenter | 0 | Enable this property to make the map automatic center and zoom to fit all markers. |

## Placeholders
| name | description |
| --- | --- |
| gsl.form | Shows the Search Form. Place it anywhere after the snippet-call. |
| gsl.map | Shows the Map. Place it anywhere after the snippet-call. |
| gsl.stores | Shows the Stores. Place it anywhere after the snippet-call. |
| gsl.message | Shows the Message of address Search. |
| total | Total number of Stores matching the Search. |
| placeholders | Call this Placeholder anywhere inside: &tplStore to see all available Placeholders. |

## Store Template Placeholders
| name | description |
| --- | --- |
| gsl.lat | Latitude of the store. |
| gsl.lng | Longitude of the store |

## Examples
A simple Example of the minimum SnippetCall
```
[[!GoogleStoreLocator]]
[[!+gsl.form]]
[[!+gsl.map]]
[[!+gsl.message]]
[[!+gsl.stores]]
```

A simple Example of changing the default TVs for the Adresss:
```
[[!GoogleStoreLocator?
    &tvNameZipcode=`yourTvName`
    &tvNameCity=`yourTvName`
    &tvNameStreet=`yourTvName`
    &tvNameHousenumber=`yourTvName`
    &tvNameState=`yourTvName`
    &tvNameCountry=`yourTvName`
]]
[[!+gsl.form]]
[[!+gsl.map]]
[[!+gsl.stores]]
```

Using the extra getPage for pagination:
```
[[!getPage?
    &element=`GoogleStoreLocator`
    &totalVar=`gsl.totalResult`
]]
```

Preordering the stores by the location "Berlin" and show only stores inside the radius of 100km. Also filtering all stores by the TV "storeType" set to "flagstore".
```
[[!GoogleStoreLocator?
    &location=`Berlin, Germany`
    &radius=`100`
    &where=`{"storeType:=":"flagstore"}`
]]
```

## Issues
If you have more than 50 stores the initail rendering of all stores will take a long time. Because the Google-API can only handle single addresses, the initial rendering will probably cause some server timeouts. The best way to handle this is just to wait a about 1 Minut and refresh the browser. After this refresh all stores should be rendert and stored inside the cache.
I tested the extra with about 1500 stores. The first rendering took about 4 refreshes and 1 Minute in between every refresh. After that: everything runs smooth.
Change a store/resource in the manager will delete this store from the cache and will be rewritten after the next request of the StoreLocation snippet. This won't take long, because it is just rendering the new added or changed stores.
If you manually clear the complete site-cache it also clears the GoogleStoreLocator cache and it needs a new rendering of all stores.

# Upgrading from Version 1.x to 2.x
Upgrading from Version 1.x to 2.x needs some little Changes in the propertie-setup.
1. the propertie `&tvNameZip` changed to `&tvNameZipcode`
2. the tv `gslZip` changed to `gslZipcode`. If you used the default tv's, just add the propertie `&tvNameZipcode="gslZip"` to the snippetcall. That way you can still use the old tv.
3. The `gslHousenumber` tv is added in 2.x. IN 1.x you just write the housenumber in the `gslStreet` tv. You still can do that. So just ignore the housenumber-tv and leave it blank or better just disable it from your store-template.
Now everything should work as before.

