import express from 'express';
import session from 'express-session';
import registrationRoute from "./routes/frontend/registrationRoute.js";
import challengeRoute from "./routes/frontend/challengeRoute.js";
import userRoute from "./routes/frontend/userRoute.js";
import genericRoute from "./routes/frontend/generalRoute.js";


const app = express();
app.use(session({secret: process.env.SECRET || "IDIOT SANDWICH, YOU FORGOT THE SECRET"}));

app.set('views', './build/views');
app.set('frontend-engine', 'ejs');
app.use(express.static('public'));
app.use(express.urlencoded({ extended: false }));


app.use("/", registrationRoute);
app.use("/", challengeRoute);
app.use("/", userRoute);
app.use("/", genericRoute);


app.listen(3000);